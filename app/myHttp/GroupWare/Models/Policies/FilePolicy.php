<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;


class FilePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, MyFile $file) {
        //
        // return $user->id == $file->user->id;
        return TRUE;
    }

    public function create(User $user) {
        //
        // dump( 'MyFilePolicy@create', $user->is_user(), $user->is_admin() ) ;
        return $user->is_user();
    }

    public function update(User $user, MyFile $file ) {
        return $user->id == $file->user->id;
    }

    public function delete( User $user, MyFile $file ) {
        
        return ( $user->id == $file->user_id )
                    ? Response::allow()
                    // : Response::deny( redirect( url()->previous() )->with( 'flush_message', '他者が');
                    : Response::deny( '他者がアップロードしたファイルは削除できません');
    }

    public function restore(User $user, MyFile $file) {
        return false;
    }

    public function forceDelete(User $user, MyFile $file) {
        return false;
    }
}
