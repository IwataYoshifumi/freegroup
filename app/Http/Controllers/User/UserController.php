<?php

namespace App\Http\Controllers\User;

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
use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{

    public function index( Request $request ){
        //  初期値の設定
        //
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $show = $request[ 'show' ];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'retired' => false, ];
            
            $sort = [];
            $show = [ 'dept_id' => 'dept_id' ,'email' => 'email', 'grade' => 'grade' ];
            $asc_desc = [];
        }
        // $show = array_merge( $show, [ 'name', 'email' ]);
        // if( ! empty( $find['retired'] )) {
        //     $show = array_merge( $show, [ 'retired' => 'retired', 'date_of_retired' => 'date_of_retired' ]);
        // }        
        
        // 検索実行
        //
        $users = User::search( $find, $sort, $asc_desc );

        //　戻るボタンの戻先設定
        //
        BackButton::setHere( $request );
        
        // 表示ビューの切り替え
        //
        $view =  Route::currentRouteName();

        return view( $view )->with( 'users', $users )
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

        $users = User::search( $find, $sort, $asc_desc )->toArray();
// dd( $users );

        return OutputCSV::input_array( [ 'lists' => $users ] );
    }

    public function create() {
        BackButton::stackHere( request() );
        return view( 'user.create' );
    }

    public function store( UserRequest $request ) {
        $user = DB::transaction( function() use( $request ) {
            $user = new User();
        
            $user->name     = $request['name'];
            $user->email    = $request['email'];
            $user->dept_id  = $request['dept_id'];
            $user->grade    = $request['grade'];
            $user->password = Hash::make($request['password']);
            $user->save();
            return $user;
        }); 

        session()->flash( 'flash_message', "ユーザ". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route('user.index', [ 'user' => $user ]);
    }

    public function show(User $user) {
        //
        BackButton::stackHere( request() );
        return view( 'user.show' )->with( 'user', $user );
    }
    
    public function detail( User $user ) {
        
    }

    public function edit(UserRequest $request, User $user) {
        //
        // if_debug( 'user.edit');
        return view( 'user.edit' )->with( 'user', $user );

    }

    public function update( UserRequest $request, User $user) {
        
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->dept_id  = $request->dept_id;
        $user->grade    = $request->grade;
        
        if( ! empty( $request->password )) {
            $user->password = Hash::make( $request->password );
        }
        if( empty( $request->retired )) {
            $user->retired = false;
            $user->date_of_retired = null;
        } else {
            $user->retired = true;
            $user->date_of_retired = $request->date_of_retired;
        }
        $user->save();
        // dd( $request, $user );
        Session::flash( 'flash_message', "ユーザ情報を変更しました" );
        session()->regenerateToken();
        return redirect()->route( 'user.show', [ 'user' => $user ] );
    }
    
    public function retire() {
        //
        return view( 'layouts.dd' );
    }
    
    public function destroy(User $user) {
        //
    }
    
    // パスワード変更画面
    //
    public function password( ) {
        
        $user = auth('user')->user();
        return View( 'user.password' )->with( 'user', $user );
        
    }
    
    // パスワード変更実行
    //
    public function updatePassword( UserRequest $request ) {
        
        $user = auth('user')->user();
        if( empty( $user ) ) { abort( 403, 'UserController.updatePassword: エラー'); }
        $user = DB::transaction( function() use( $user, $request ) {
            $user->password = Hash::make( $request['password'] ) ;
            $user->save();
            return $user;
        }); 

        Session::flash( 'flash_message', "パスワードを変更しました。" );
        session()->regenerateToken();
        return redirect()->route('user.change_password' );
        // return view( 'user.password' );
        
    }
    
    // Ajaxフォーム用
    //
    public function json( Request $request ) {
        $name    = $request->name;
        $dept_id = $request->dept_id;
        // if_debug( $request->all() );
        
        if( is_null( $name ) and empty( $dept_id )) { return response()->json( [] ); }
        
        $users = User::with('dept');
        
        if( ! empty( $name )) {
            $users = $users->where( 'name', 'like', '%'.$name.'%' );
        }
        if( ! empty( $dept_id )) {
            $users = $users->where( 'dept_id', $request->dept_id );
        }
        $users = $users->where( 'retired', false );

        // if_debug( 'aa', $request->dept_id );
        $users = $users->get();
        // if_debug( $users );
        $array = [];
        foreach( $users as $user ) {

            array_push( $array, [   'id'        => $user->id,
                                    'name'      => $user->name, 
                                    'grade'     => $user->grade,
                                    'dept_id'   => $user->dept_id,
                                    'dept_name' => $user->dept->name, 
                                    ] );
            
        }

        return response()->json( $array );
        // return response()->json( $users );
        // dd( $users, response()->json( $users ));
    }
}
