<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\User as OriginalUser;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

use App\Http\Controllers\User\UserController as OriginalUserController;

class UserController extends OriginalUserController {
    
    public function mySelf() {
        $user = OriginalUser::find( auth( 'user')->id() );
        BackButton::setHere( request() );
        return self::detail( $user );
    }
    
    public function show( OriginalUser $user ) {
        
    }

    public function detail( OriginalUser $user ) {
        $user = User::find( $user->id );
        Backbutton::stackHere( request() );
        return view( 'groupware.user.detail' )->with( 'user', $user );
    }
    
    public function deleted( OriginalUser $user ) {
        $user = User::find( $user->id );
        
        DB::transaction( function() use( $user ) {
            
            $user->schedules()->detach();
            $user->reports()->detach();
            $user->delete();
        });
        
        // $user->delete();

        session()->regenerateToken();
        BackButton::removePreviousSession();
        return view( 'user.delete')->with( 'user', $user );
        
    }
    
}
