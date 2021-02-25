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

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Requests\InitAllUsersRequest;
use App\myHttp\GroupWare\Jobs\Init\InitAllUsersJob;

class InitController extends Controller {

    public function showForminitAllUsers() {
        
        dump( old() );
        return view( 'groupware.init.init_all_users' );
    }

    public function initAllUsers( InitAllUsersRequest $request ) {

        //　初期化実行
        //
        InitAllUsersJob::dispatch();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', '初期化処理を実行しました。結果を確認してください。');
        return view( 'groupware.init.init_all_users' );
        // return redirect()->route( 'groupware.user.index' );

        
    }
   
}
