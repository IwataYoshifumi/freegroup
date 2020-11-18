<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\VacationList;
// use App\Models\Vacation\Paidleave;
use App\Http\Requests\Vacation\VacationRequest;

use App\Http\Helpers\BackButton;

class VacationController extends Controller
{
    //  有給割当社員の選択
    //
    public function select( Request $request ) {
        
        if( isset( $request->find )) {
            
            $find = $request->find;
            if( !empty( $find['year'] )) {
                
                //  有給割当済みUserIDの取得
                //
                $allocated_users = Vacation::getArrayAllocatedUser( $request->find['year'] );

                if( is_array( $allocated_users ) && count( $allocated_users) >= 1 ) {
                    
                    if( $find['allocatedOrNot'] == "未割当" ) {
                        $find['except_user_id'] = $allocated_users;
                    } else {
                        $find['include_user_id'] = $allocated_users;
                    }
                }
            }
            $include_users = User::getUserList( $find );
        } else {
            $find = [ 'pagination' => 10 ];
        }
        if( isset( $include_users ) && is_array( $include_users )) {
            array_push( $find, [ 'include_user_id' => $include_users ]);
        }

        $users = User::getUserList( $find );
        $users->appends( ['find' => $find ]);
        BackButton::setHere( $request );
        
        return view( 'vacation.allocate.selectUsers' )->with( 'find', $find )
                                              ->with( 'users',  $users );
    }
    
    //  有給入力画面
    //
    public function create( Request $request ) {

        // 割当ユーザの検索
        //
        $users = User::whereIn( 'id', $request->users )->get();

        return view( 'vacation.allocate.create' )->with( 'request', $request )
                                                 ->with( 'users', $users );
    }

    //  有給休暇　割当情報の登録
    //
    public function store( VacationRequest $request ) {

        // INSERT用データ作成
        //
        // dd( $request );
        $datum = [  'action'            => '割当',
                    'type'              => "有給休暇",
                    'year'              => $request->year,
                    'allocate_date'     => $request->allocate_date,
                    'expire_date'       => $request->expire_date,
                    'num'               => $request->num,
                    // 'allocated_num'     => $request->num,
                    // 'remains_num'       => $request->num,
                    // 'application_num'   => 0,
                    // 'approval_num'      => 0,
                    // 'completed_num'     => 0,
                    // 'expired_num'       => 0,
                ];
                
                //  allocated_num | remains_num | application_num | approval_num | completed_num | expired_num
                
        $users = $request->users;
        $i = 0;
        foreach( $request->users as $user_id ) {
            $datum['user_id'] = $user_id;
            $data[$i] = $datum;
            $i++;
        }
        $list = [ [ 'action' => '残日数', 'num' => $request->num ],
                  [ 'action' => '割当'  , 'num' => $request->num * -1 ],
                  ];

        //  データベースへ追加
        //
        DB::transaction( function() use ( $data, $list ) {
            
            foreach( $data as $d ) {
                // dd( $d);
                $vacation = Vacation::create( $d );
                $vacation->create_lists();
                // $vacation->lists()->createMany( $list );
                $vacation->paidleave_recalculate();
            }
            // $result = DB::table( 'vacation' )->insert( $data );    
        });
        
        $where = [ 'year'      => $request->year, 
                   'user_id'   => $request->user_id ];
               
        Session::flash( 'flash_message', "有給休暇を割り当てました" );
        session()->regenerateToken();
        return redirect()->route( 'vacation.vacation.index', [ 'where' => $where ]);
        
    }

    //  有給割当状況　一覧
    //
    public function index( Request $request ) {

        if( empty( $request->find )) {
            $find['pagination'] = 20;
        } else {
            $find = $request->find;
        }

        $paidleaves = Vacation::DBselect( $find );
        // dump( $paidleaves);

        BackButton::setHere( $request );
        return View( 'vacation.allocate.index' )->with( 'paidleaves', $paidleaves )
                                        ->with( 'request',    $request    );
        
    }

    //  有給休暇　割当情報　詳細表示
    //
    public function show( Vacation $vacation ) {
        // dd( $vacation );
        // $paidleave = Vacation::find( $paidleave );
        return View( 'vacation.allocate.show ')->with( 'paidleave', $vacation );
        
    }

    //  割当済み有給休暇の修正
    //
    public function edit( Vacation $vacation ) {
        // $paidleave = Vacation::find( $paidleave );
        // dd( $paidleave);
        return View( 'vacation.allocate.edit ')->with( 'paidleave', $vacation );
        
    }

    //  割当済み有給休暇の修正登録
    //
    public function update( VacationRequest $request, Vacation $vacation ) {
        
        DB::transaction( function() use( $request, $vacation ) {
            // Update用データ作成
            // 休暇申請に対して、残り数を計算
            //
            // dd( $vacation );
            
            VacationList::where( 'vacation_id', $vacation->id )->delete();
            // dump( $request );
            //  dd(         Vacation::where( 'user_id', $vacation->user_id )->where( 'year'   , $vacation->year )->get()->all() );
            if( $vacation->year != $request->year ) {

                Vacation::where( 'user_id', $vacation->user_id )
                        ->where( 'year'   , $vacation->year )
                        ->update( [ 'year'=> $request->year ]);
            }
            
            $vacation->year                = $request->year;
            $vacation->allocate_date       = $request->allocate_date;
            $vacation->expire_date         = $request->expire_date;
            $vacation->allocated_num       = $request->num;
            $vacation->num                 = $request->num;
            $vacation->done_expired        = false;
            $vacation->save();

            $vacation->create_lists();
            $vacation->paidleave_recalculate();
            
        });

        Session::flash( 'flash_message', "有給休暇割当情報を修正しました。" ); 
        session()->regenerateToken();
        // return redirect()->route( 'vacation.show', [ 'vacation' => $vacation ]);
        return redirect()->route( 'vacation.vacation.show' ,['vacation'=> $vacation] );
    }


    


    
}
