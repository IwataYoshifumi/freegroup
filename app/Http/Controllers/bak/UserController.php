<?php

namespace App\Http\Controllers;

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
use App\Http\Requests\UserRequest;
use App\User;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
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
    
    
    
    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'user.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( UserRequest $request )
    {
        $user = DB::transaction( function() use( $request ) {
            $user = new User();
        
            $user->name     = $request['name'];
            $user->email    = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->save();
            return $user;
        }); 

        session()->flash( 'flash_message', "ユーザ". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        return redirect()->route('user.index', [ 'user' => $user ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return view( 'user.show' )->with( 'user', $user );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(UserRequest $request, User $user)
    {
        //
        return view( 'user.edit' )->with( 'user', $user );

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update( UserRequest $request, User $user)
    {
        
        $user->name = $request->name;
        $user->email = $request->email;
        
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
    
    public function retire()
    {
        //
        return view( 'layouts.dd' );
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
