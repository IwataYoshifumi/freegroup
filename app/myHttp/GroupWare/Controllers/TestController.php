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

class TestController extends Controller {

    //　Files コンポーネント開発用ルート
    //
    public function files( Request $request ) {
        
        if( ! is_null( $request->all() )) {
            $files = MyFile::where( 'user_id', user_id() )->doesntHave( 'fileables' )->get();;
        } else {
            $files = [];
        }
        // $files = $files->pluck('id')->toArray();
        if_debug(  $request->all(), old(), $files );
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files', $files );
        return view( 'groupware.file.test' )->with( 'request', $request )
                                            ->with( 'component_input_files', $component_input_files )
                                            ->with( 'files',   $files   );
    }
    public function filesUpdate( TestRequest $request ) {
        
        if_debug( $request->all(), $request->attach_files );

    }
    
}
