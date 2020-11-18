<?php

namespace App\myHttp\GroupWare\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use DB;


use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Requests\FileRequest;


class FileController extends Controller {

    public function index( Request $request ) {

        if( isset( $request->find )) { 
            $find = $request->find; 
            $find['users'] = ( is_array( $request->users )) ? $request->users : [];
            $files = MyFile::search( $find, $find['pagination'] );
        } else {
            $user = auth('user')->user();
            $find['users'] = [ $user->id ];
            $find['pagination'] = 30;
            $files = [];
            // dump( $find );
            
        }
        // dump( $files );

        BackButton::setHere( $request );
        return view( 'groupware.file.index' )->with( 'files', $files )
                                             ->with( 'request', $request )
                                             ->with( 'find', $find   );
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
    
    public function select( Request $request ) {
        return $this->index( $request );
    }

    public function show( MyFile $file ) {
        $headers = [ 'Content-Disposition' => 'attachment; filename="'.$file->file_name.'"' ];
        // return response()->file( storage_path( 'app/' ).$file->path, $headers );
        return response()->file( storage_path( 'app/' ).$file->path  );
    }
    
    public function detail( MyFile $file ) {
        $this->authorize( 'view', [ $file, auth( 'user' )->user() ]);
        BackButton::stackHere( request() );
        return view( 'groupware.file.detail' )->with( 'file', $file );
    }
    
    public function download( MyFile $file ) {
        return response()->download( storage_path( 'app/' ).$file->path, $file->file_name );
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
    
    //　削除の認可処理
    //
    private function authorize_delete_files( $files ) {
        // dump( Auth::guard() );
        // $user = auth( 'user' )->user();
        $user = Auth::user();
        foreach( $files as $file ) { 
            $this->authorize( 'delete', $file, $user ); 
        }
    }
   
}
