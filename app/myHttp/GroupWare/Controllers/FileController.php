<?php

namespace App\myHttp\GroupWare\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

use DB;
use Exception;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Requests\FileRequest;
use App\myHttp\GroupWare\Requests\TestRequest;

use App\myHttp\GroupWare\Models\Actions\FileAction;
use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;


use  App\myHttp\GroupWare\Controllers\Search\SearchFile;

class FileController extends Controller {

    public function index( Request $request ) {

        if( $request->find ) {
            $files = SearchFile::search( $request );
        } else {
            $request->users = [ user_id() ];
            $request->pagination = 30;
            $files = [];
        }

        BackButton::setHere( $request );
        return view( 'groupware.file.index' )->with( 'files', $files )
                                             ->with( 'request', $request );
    }
    
    public function select( Request $request ) {
        return $this->index( $request );
    }

    public function show( MyFile $file ) {
        return $this->detail( $file );
        
    }
    
    public function detail( MyFile $file ) {

        dump( $file->getModel(), is_debug() );
        
        $this->authorize( 'view', $file );
        
        BackButton::stackHere( request() );
        return view( 'groupware.file.detail' )->with( 'file', $file );
    }
    
    public function download( MyFile $file, $class_name, $model_id ) {

        // dump( $class, $model, __METHOD__ );
        
        $this->authorize( 'download', [ $file, $class_name, $model_id ] );
        
        try {
            return response()->download( storage_path( 'app/' ).$file->path, $file->file_name );
        } catch( Exception $e ) {
            return response( 'ファイル読込エラーが発生しました', 200 );
        }
    }
    
    public function downloadMyFile( MyFile $file ) {

        $this->authorize( 'view', $file );
        
        try {
            return response()->download( storage_path( 'app/' ).$file->path, $file->file_name );
        } catch( Exception $e ) {
            return response( 'ファイル読込エラーが発生しました', 200 );
        }
    }

    public function delete( FileRequest $request ) {
        
        // dd( $request->all(), $request->input('files') );
        $files = MyFile::with( 'user', 'schedules', 'reports' )->find( $request->input('files') );
        
        //　認可
        //
        $this->authorize_delete_files( $files );

        BackButton::stackHere( request() );
        return view( 'groupware.file.delete' )->with( 'request', $request )
                                              ->with( 'files',   $files   );
    }

    public function deleted( FileRequest $request ) {
        
        $files = MyFile::with( 'user', 'schedules', 'reports' )->find( $request->input('files') );
        
        //　認可
        //
        $this->authorize_delete_files( $files );
        
        //　データベースの削除
        //
        $files = DB::transaction( function() use ( $files, $request ) {
    
            // $files = MyFile::with( 'user', 'schedules', 'reports' )->find( $request->input('files') );
            foreach( $files as $file ) {
                $file->reports()->detach();
                $file->schedules()->detach();
            }
            MyFile::destroy( $request->input('files') );
            return $files;
        });
        //　ファイルを消去
        //
        foreach( $files as $file ) {
            Storage::delete( $file->path );
        }


        session()->regenerate();
        Session::flash( 'flash_message', "ファイルを削除しました。" );
        BackButton::removePreviousSession();
        return view( 'groupware.file.delete' )->with( 'request', $request )
                                              ->with( 'files',   $files   );
    }

    //　AJAX ファイルアップロードを受ける（ InputFilesComponent2 で使用　）
    //
    public function uploadAPI( Request $request ) {

        $uploaded_file = $request->file('upload_file');
        
        $path  = $uploaded_file->store('');
        $value = [ 'file_name' => $uploaded_file->getClientOriginalName(), 'path' => $path, 'user_id' => $request->user_id  ];
        $file  = MyFile::create( $value );
        $value['file_id'] = $file->id;

        return response()->json( $value );        
    }

    //　AJAX アップロードファイルを削除（ InputFilesComponent2 で使用　）
    //
    public function deleteAPI( Request $request ) {

        // 添付ファイル、他人のファイルは削除削除できない
        //
        if( user_id() != $request->user_id ) { return response()->deny(); }
        $file = MyFile::where( 'id', $request->file_id )->where( 'user_id', $request->user_id )->doesntHave( 'fileables' )->get();
        if( $file->count() != 1 ) { return response()->deny(); }
        
        FileAction::force_delete( $file->first() );
        return response()->json( [ 'result' => 'success' ] );
    }

    
    public function json_search( Request $request ) {

        $find['users']      = [ $request->user_id ];
        $find['start_date'] = $request->start_date;   
        $find['end_date']   = $request->end_date;
        $find['file_name']  = $request->file_name;
        $find['attached']   = $request->attached;
        $find['search_mode']= 1;
    
        $pagination = $request->pagination;
        
        $files = MyFile::search( $find );
        
        $array = [];
        foreach( $files as $f ) {
            array_push( $array, [   'id' => $f->id, 
                                    'file_name' => $f->file_name,
                                    'created_at' => $f->p_created_at(),
                                    'url'       => route( 'groupware.file.show',  [ 'file' => $f->id ] ),
                                    ] );
        }
        #dump( $request, $files );
        return response()->json( $array );
    }
    
    public function deleteAllUntachedFiles() {
        if( ! is_debug() ) { return Response::deny(); }
        
        FileAction::delete_all_detached_files();
        session()->flash( 'info_message', "添付されていないファイルを全て削除しました" );
        return redirect()->route( 'groupware.file.index' );
        
    }
    
    //　削除の認可処理
    //
    private function authorize_delete_files( $files ) {
        $user = Auth::user();
        foreach( $files as $file ) { 
            if( ! $this->authorize( 'delete', $file, $user ) ) {
                return Response::deny( 'Files which are not yours includes');
            } 
        }
    }
   
}
