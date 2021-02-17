<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Fileable;
use App\myHttp\GroupWare\Models\AccessList;

class FileAction  {
    
    public static function save( $upload_file, $file_name = null ) {
 
        //　ファイル保存して、MyFileのインスタンスを返す
        //
        if( empty( $upload_file )) { return null; }

        $path = $upload_file->store('');
        $name = ( ! empty( $file_name )) ? $file_name : $upload_file->getClientOriginalName();
        
        $value = [ 'file_name' => $name, 'path' => $path, 'user_id' => user_id() ];
        $file = MyFile::create( $value );
        return $file;

    }
    
    public static function delete( MyFile $file ) {
        die( __METHOD__ );

        # ファイルが添付されていたら削除不可。

    }

    /*
     *
     *　ファイル強制削除（添付リレーションも削除）
     *
     */
    public static function force_delete( MyFile $file ) {
        $file_name = $file->file_name;
        
        DB::table( 'fileables' )->where( 'file_id', $file->id )->delete();
        Storage::delete( $file->path );
        return $file->delete();
    }

    /*
     *
     *　アッタッチされていないファイルを全て削除する
     *
     */
    public static function delete_all_detached_files() {
        
        
        // $files = new MyFile;
        // $files = $files->doesntHave( 'fileables' )->get();
        $files = MyFile::whereDoentAttached()->get();        

        if( count( $files ) == 0 ) { return 0; }
                
        try {
            Storage::delete( $files->pluck( 'path' )->toArray() );
        } catch( Exception $e ) {
            throw new Exception( 'File Delete Error ');
        }
        return $files->toQuery()->delete();
    }    
    
}

