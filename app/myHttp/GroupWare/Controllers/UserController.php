<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use DB;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\ScreenSize;

use App\myHttp\GroupWare\Requests\UserRequest;
use App\myHttp\GroupWare\Models\Actions\UserAction;
use App\myHttp\GroupWare\Models\Initialization\InitUser;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\AccessList;



class UserController extends Controller {
    
    //////////////////////////////////////////////////////////////////////
    //
    //
    //
    ///////////////////////////////////////////////////////////////////////
    public function index( Request $request ){
        
        //  初期値の設定
        //
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $show = $request[ 'show' ];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'retired' => false, 'paginate' => 20 ];
            $sort = [];
            $show = [ 'dept_id' => 'dept_id' ,'email' => 'email', 'grade' => 'grade' ];
            $asc_desc = [];
        }
        
        // 検索実行
        $users = User::search( $find, $sort, $asc_desc );

        BackButton::setHere( $request );
        return view( 'groupware.user.index' )
                            ->with( 'users', $users )
                            ->with( 'find' , $find  )
                            ->with( 'sort' , $sort  )
                            ->with( 'show' , $show  )
                            ->with( 'asc_desc', $asc_desc );
    }

    public function mySelf() {
        $user = User::find( auth( 'user')->id() );
        return self::detail( $user );
    }
    
    public function detail( User $user ) {

        //　なんらかのエラーがあればログアウト？
        //
        if( ! InitUser::init( $user )) { 
            // Auth::logout(); 
            // return back(); 
        }
        
        $user = User::find( $user->id );
        Backbutton::setHere( request() );
        return view( 'groupware.user.detail' )->with( 'user', $user );
    }
    
    public function show( User $user ) {
        BackButton::stackHere( request() );
        return view( 'groupware.user.show' )->with( 'user', $user );
    }
    
    public function create() {
        $this->authorize( 'create', User::class );
        
        $user = new User;
        
        BackButton::stackHere( request() );        
        return view( 'groupware.user.input')->with( 'user', $user );
    }
    
    public function store( UserRequest $request ) {
        $this->authorize( 'create', User::class );
        
        $user = UserAction::creates( $request );
        
        session()->flash( 'flash_message', "ユーザ". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route('groupware.user.show', [ 'user' => $user->id ]);
        // return view( 'groupware.user.show' )->with( 'user', $user );
    }
    
    public function edit( User $user ) {
        $this->authorize( 'update', $user );
        
        BackButton::stackHere( request() );
        return view( 'groupware.user.input')->with( 'user', $user );
    }
    
    public function update( UserRequest $request, User $user ) {
        $this->authorize( 'update', $user );
        
        $user = UserAction::updates( $user, $request );
        

        session()->flash( 'flash_message', "ユーザ". $request['name']. "を修正しました" );
        session()->regenerateToken();
        BackButton::removePreviousSession();

        // return view( 'groupware.user.show' )->with( 'user', $user );
        return redirect()->route('groupware.user.show', [ 'user' => $user->id ]);
    }


    
    public function deleted( User $user ) {
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
