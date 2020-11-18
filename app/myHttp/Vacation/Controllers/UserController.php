<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;
use App\Http\Controllers\Controller;

use App\Http\Requests\Vacation\UserRequest;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Http\Helpers\BackButton;

class UserController extends Controller
{
    //
    // protected function validator(array $data) {
        
    //     return Validator::make($data, [
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'dept_id' => ['required'],
    //     ]);
    // }
    
    function index( Request $request ) {
        
        //dd( $depts );
        if( isset( $request->find )) {
            $find = $request->find;
        } else {
            $find = [ 'retired'   => false, 
                      'show_item' => [ '部署名' => '部署名', '職級' => '職級', '閲覧権限' => '閲覧権限', '管理者' => '管理者', 
                                        'メール' => 'メール', '退社' => '退社' ],
                      'pagination'=> 10,
                      ];
        }
        
        $users = User::getUserList( $find );    

        
        //　Rootルートの設定
        //
        BackButton::setHere( $request );

        return view( 'vacation.users.index' )->with( 'find', $find )
                                             ->with( 'request',$request )
                                             ->with( 'users',  $users   );
        

    }
    
    function show( User $user, Request $request ) {
        
            return view( 'vacation.users.show' )->with( 'user', $user )
                                       ->with( 'request', $request );
                                      
    }
    
    function detail_mySelf( Request $request ) {
        BackButton::setHere( $request );
        return self::detail( User::find( Auth::id() ), $request );
        
    }
    
    function detail( User $user, Request $request ) {

        //　直近半年の申請休暇を検索
        //
        $date = new Carbon( '-6 months' );
        $six_month_ago = $date->toDateString();
        $applications = Application::where( 'user_id', $user->id )
                                   ->where( 'date', '>=', $six_month_ago )
                                   ->orderby( 'start_date', 'desc' )
                                   ->get();
        
        //  承認済みの申請があれば、完了処理を促すメッセージを表示する
        //
        foreach( $applications as $app ) {
            if( $app->status == "承認" ) {
                Session::flash( "info_message", "承認済みの休暇申請があります。休暇を取得したら完了処理をしてください。<BR>
                                                 <font color='red'>完了処理をしないと総務で休暇取得状況を把握できず、欠勤扱いになります。</font>" );
                break;
            }
        }
        
        return view( 'vacation.users.detail' )->with( 'user', $user )
                                     ->with( 'applications', $applications );
    }
    
    function create() {
        return view( 'vacation.users.create' );
    }
    
    function store( UserRequest $request ) {
        $user = DB::transaction( function() use( $request ) {
            $user = new User();
        
            $user->code     = $request['code'];
            $user->name     = $request['name'];
            $user->email    = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->join_date= $request['join_date'];
            $user->dept_id  = $request['dept_id'];
            $user->grade    = $request['grade'];
            $user->carrier  = $request['carrier'];
            $user->browsing = $request['browsing'];
            $user->memo     = $request['memo'];
            $user->admin    = FALSE;
            $user->retired  = FALSE;
            $user->save();
            return $user;
        }); 

        Session::flash( 'flash_message', $request['name']."を追加しました。" );
        session()->regenerateToken();
        return redirect()->route('vacation.user.show', [ 'user' => $user ]);
        
        
    }
    
    //　修正画面の表示
    //
    function edit( Request $request, User $user ) {

        return View( 'vacation.users.edit' )->with( 'user', $user );
    }
    
    //  修正実行
    //
    function update( UserRequest $request, User $user ) {

        DB::transaction( function() use( $user, $request ) {
            User::updateDB( $user, $request );
        });
        
        Session::flash( 'flash_message', $request['name']."を修正しました。" );
        
        session()->regenerateToken();
        return redirect()->route( 'vacation.user.show', ['user' => $user ] );
    }
    
    // パスワード変更画面
    //
    public function password( ) {
        
        $user = auth( 'user')->user();
        return View( 'vacation.users.password' )->with( 'user', $user );
        
    }
    
    // パスワード変更実行
    //
    public function updatePassword( User $user, UserRequest $request ) {
        
        $user = DB::transaction( function() use( $user, $request ) {
            $user->password = Hash::make( $request['password'] ) ;
            $user->save();
            return $user;
        }); 

        Session::flash( 'flash_message', "パスワードを変更しました。" );
        session()->regenerateToken();
        // return redirect()->route('user.password', [ 'user' => $user ]);
        return view( 'vacation.users.password' )->with( 'user' , $user );
        
    }
    
}
