<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;

class AdminController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        //  初期値の設定
        //
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $show = $request[ 'show' ];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'retired' => false ];
            $sort = [];
            $show = [ 'email' ];
            $asc_desc = [];
        }
        $show = array_merge( $show, [ 'name', 'email' ]);
        if( ! empty( $find['retired'] )) {
            $show = array_merge( $show, [ 'retired' => 'retired', 'date_of_retired' => 'date_of_retired' ]);
        }        
        
        // 検索実行
        //
        $admins = Admin::search( $find, $sort, $asc_desc );

        //　戻るボタンの戻先設定
        //
        BackButton::setHere( $request );
        
        // 表示ビューの切り替え
        //
        $view =  Route::currentRouteName();

        return view( $view )->with( 'admins', $admins )
                            ->with( 'find' , $find  )
                            ->with( 'sort' , $sort  )
                            ->with( 'show' , $show  )
                            ->with( 'asc_desc', $asc_desc );
    }
    
    public function select( Request $request ) {
        return $this->index( $request );
    }
    
    public function csv( Request $request ) {
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'retired' => false ];
            $sort = [];
            $asc_desc = [];
        }        
        $find['pagination'] = 0;

        $admins = Admin::search( $find, $sort, $asc_desc )->toArray();
// dd( $admins );

        return OutputCSV::input_array( [ 'lists' => $admins ] );
    }

    public function create() {
        BackButton::stackHere( request() );
        return view( 'admin.create' );
    }

    public function store( AdminRequest $request ) {
        $admin = DB::transaction( function() use( $request ) {
            $admin = new Admin();
        
            $admin->name     = $request['name'];
            $admin->email    = $request['email'];
            $admin->password = Hash::make($request['password']);
            $admin->save();
            return $admin;
        }); 

        session()->flash( 'flash_message', "ユーザ". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route('admin.index', [ 'admin' => $admin ]);
    }

    public function show(Admin $admin) {
        //
        BackButton::stackHere( request() );
        return view( 'admin.show' )->with( 'admin', $admin );
    }

    public function edit(AdminRequest $request, Admin $admin) {
        //
        BackButton::stackHere( $request );
        return view( 'admin.edit' )->with( 'admin', $admin );

    }

    public function update( AdminRequest $request, Admin $admin) {
        
        $admin->name = $request->name;
        $admin->email = $request->email;
        
        if( ! empty( $request->password )) {
            $admin->password = Hash::make( $request->password );
        }
        if( empty( $request->retired )) {
            $admin->retired = false;
            $admin->date_of_retired = null;
        } else {
            $admin->retired = true;
            $admin->date_of_retired = $request->date_of_retired;
        }
        $admin->save();
        // dd( $request, $admin );
        Session::flash( 'flash_message', "ユーザ情報を変更しました" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'admin.show', [ 'admin' => $admin ] );
    }
    
    public function retire() {
        //
        return view( 'layouts.dd' );
    }
    
    public function destroy(Admin $admin) {
        //
    }
    
    // パスワード変更画面
    //
    public function password( ) {
        
        $admin = auth('admin')->user();
        return View( 'admin.password' )->with( 'admin', $admin );
        
    }
    
    // パスワード変更実行
    //
    public function updatePassword( AdminRequest $request ) {
        
        $admin = auth('admin')->user();
        if( empty( $admin ) ) { abort( 403, 'AdminController.updatePassword: エラー'); }
        $admin = DB::transaction( function() use( $admin, $request ) {
            $admin->password = Hash::make( $request['password'] ) ;
            $admin->save();
            return $admin;
        }); 

        Session::flash( 'flash_message', "パスワードを変更しました。" );
        session()->regenerateToken();
        return redirect()->route('admin.change_password' );
        // return view( 'admin.password' );
        // return redirect()->route( 'admin.home');        
    }
}
