<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Admin;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Calprop;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\File as MyFile;


class FilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, MyFile $file) {
        if( $user->id == $file->user_id ) { return Response::allow(); }
        return Response::deny();
    }

    // ファイルのダウンロードはアップロード者 or 各モデルのリーダーしかアクセス出来ない
    //
    public function download( User $user, MyFile $file, $class_name, $model_id ) {
        
        if( $class_name == 'schedule' ) { $model = Schedule::find( $model_id ); } 
        if( $class_name == 'report'   ) { $model = Report::find(   $model_id ); }
        if( $class_name == 'calprop'  ) { $model = CalProp::find(  $model_id ); }
        if( $class_name == 'task'     ) { $model = Task::find(  $model_id );    }
        if( $class_name == 'facility' ) { $model = Facility::find(  $model_id );    }
        
        if( ! $model instanceof Schedule and 
            ! $model instanceof Report   and
            ! $model instanceof Calprop  and
            ! $model instanceof Task     and
            ! $model instanceof Facility 
            ) { die( __METHOD__ . 'Invalid Input' ); } 
        
        if( $user->id == $file->user_id ) { return Response::allow(); }
        if( $model->canRead( $user )) { return Response::allow(); }
        // if( $user->can( 'view', $model )) { return Response::allow(); }
        
        return Response::deny( 'FilePolicy@download : Invalid Access');
    }

    public function delete( User $user, MyFile $file ) {
        return $this->view( $user, $file );
    }

    public function forceDelete(User $user, MyFile $file) {
        return $this->view( $user, $file );
    }

    public function deleteAllUntachedFiles( $user ) {
        if( $user instanceof Admin ) { return Response::allow(); }
        if( is_debug() ) { return Response::allow(); }
        return Response::deny();
    }
    
    public function deleteUntachedFiles( User $user ) {
        return true;
    }
    
}
