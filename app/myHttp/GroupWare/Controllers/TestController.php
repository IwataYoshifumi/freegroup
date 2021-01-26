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

use App\myHttp\GroupWare\Jobs\File\DeleteFilesJob;

use  App\myHttp\GroupWare\Controllers\Search\SearchFile;

class TestController extends Controller {

    /*
     *
     * 開発テスト用テンプレートコントローラ（コピペして使う）
     *
     */
    public function template( Request $request ) {
        return view( 'groupware.develop.template' )->with( 'request', $request );
    }

    //　カスタムBladeテスト
    //
    public function icons( Request $request ) {
        return view( 'groupware.test.custome_blade_icons' )->with( 'request', $request );
    }


    public function test( Request $request ) {
        return view( 'groupware.test.test' )->with( 'request', $request );
        // return view( 'groupware.test.depts' )->with( 'request', $request );
    }
    
    public function testDeptUserCustomer( Request $request ) {
        // return view( 'groupware.test.test' )->with( 'request', $request );
        
        
        return view( 'groupware.test.depts_users_customers' )->with( 'request', $request );
    }
    

    // Files Delete 開発用ルート
    //
    public function deleteFiles( Request $request ) {
        
        $files = MyFile::doesntHave( 'fileables' )
                        ->orWhere( 'id', '>=', 351 )
                        ->with( 'fileables' )
                        ->get();
        if_debug( __METHOD__, $files );
        DeleteFilesJob::dispatch( $files );

    }

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
