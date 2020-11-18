<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;
use DB;

use App\Http\Requests\Vacation\ApprovalMasterRequest;
use App\Models\Vacation\User;
use App\Models\Vacation\ApprovalMaster;
use App\Models\Vacation\ApprovalMasterAllocate;
use App\Http\Helpers\BackButton;

class ApprovalMasterController extends Controller
{
    //
    function create() {
        return view( 'vacation.approvalMaster.create' );
        
    }
    
    function store( ApprovalMasterRequest $request ) {

         $master = DB::transaction( function() use ( $request ) {
            $master = new ApprovalMaster();
            $master->name = $request->name;
            $master->memo = $request->memo;
            $master->save();
        
            //　マスターリストの登録
            //
            $approvers = $request->approvers;
            $approvals = array();
            foreach( $approvers as $i => $user_id ) {
                if( ! is_null( $user_id )) {
                    $approvals[$i] = [ 'approval_master_id' => $master->id,
                                       'user_id'            => $user_id, 
                                    ];
                }
            }
            $result = DB::table('approval_master_lists')->insert( $approvals );
        
            return $master;
        });


        Session::flash( 'flash_message', "承認マスター「". $request->name." 」を追加しました" );
        Session::flash( 'previous_route', Route::currentRouteName() );

        session()->regenerateToken(); 
        return redirect()->route( 'vacation.approvalMaster.index' ,['master' => $master ] );
    }

    function index( Request $request ) {
        
        $query = new ApprovalMaster;
        if( isset( $request['find'] )) {
            $find = $request['find'];
            if( isset( $find['name']) ) {
                if( isset( $find['name_strict_search']) ) {
                    $query = $query->where( 'name', $find['name'] );
                } else {
                    $query = $query->where( 'name', 'like', '%'.$find['name'].'%' );

                }
            }
        }
        
        $masters = $query->get();
        
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        
        return view( 'vacation.approvalMaster.index' )->with( 'masters', $masters )
                                             ->with( 'request', $request );
        
    }
    
    function show( ApprovalMaster $master ) {
        // dd( $master );
        return view( 'vacation.approvalMaster.show' )->with( 'master', $master );

    }
    
    function edit( ApprovalMaster $master ) {
        
        return view( 'vacation.approvalMaster.edit' )->with( 'master', $master );
        
    }
    
    function update( ApprovalMaster $master, ApprovalMasterRequest $request ) {
        
        DB::transaction( function() use ( $master, $request ) {

            $master->name = $request->name;
            $master->memo = $request->memo;
            $master->save();
        
            //　マスターリストの登録
            //
            $approvers = $request->approvers;
            $approvals = array();
            foreach( $approvers as $i => $user_id ) {
                if( ! is_null( $user_id )) {
                    $approvals[$i] = [ 'approval_master_id' => $master->id,
                                       'user_id'            => $user_id, 
                                    ];
                }
            }
            
            DB::table( 'approval_master_lists' )->where( 'approval_master_id', $master->id )->delete();
            $result = DB::table('approval_master_lists')->insert( $approvals );
        
        });

        Session::flash( 'flash_message', "承認マスター「". $request->name." 」を修正しました。" );
        Session::flash( 'previous_route', Route::currentRouteName() );
        session()->regenerateToken(); 
        // return redirect()->route( 'vacation.approvalMaster.index' , [ 'find' => [ 'name' => $master->name, 'name_strict_search' => 1 ]] );
        return redirect()->route( 'vacation.approvalMaster.show' , [ 'master' => $master ] );

    }
    
    //　マスター割当画面
    //
    function allocate( ApprovalMasterRequest $request ) {
    
        $users = User::getUserList( [ 'include_user_id' => $request->input('users')] );    
        return view( 'vacation.approvalMaster.allocate')->with( 'users', $users );
        
    }
    
    //  マスター割当実行
    //
    function allocated( ApprovalMasterRequest $request ) {

        $master = $request->input( 'master' );
        $users  = $request->input( 'users' );
        // dd( $request, $master, $users );
        
        DB::transaction( function() use ( $master, $users ) {
            
            DB::table( 'approval_master_allocates' )->wherein( 'user_id', $users )->delete();
            
            foreach( $users as $user_id ) {
                $query[$user_id] = ['approval_master_id' => $master, 'user_id' => $user_id ];
            }

                // dd( $query );

            DB::table( 'approval_master_allocates' )->insert( $query );
        });

        Session::flash( 'flash_message', "承認マスターを割当ました" );
        session()->regenerateToken();
        return redirect()->route( "vacation.approvalMaster.show", ['master'=> $master] );

    }

    //
    //  承認マスター割当解除
    //
    public function deallocateSelectUsers( Request $request ) {
        
        BackButton::setHere( $request );    
        return $this::indexUsers( $request );
    }
    
    //
    //  承認マスター割当
    //
    public function selectUsers( Request $request ) {
        BackButton::setHere( $request );
        return $this::indexUsers( $request );
    }
    
    //
    //  承認マスター割当状況
    //
    public function indexUsers( Request $request ) {
        // dd( $master );
        
        if( isset( $request->find )) {
            $find = $request->find;
        } else {
            $find = array( 'retired' => false );
            $find['pagination'] = '30';
        }
        // 未割当社員を検索
        //
        if( isset( $request->search_not_yet_allocate )) {
            $allocate_users = ApprovalMasterAllocate::all( 'user_id' )->toArray();
            $find['except_user_id'] = $allocate_users;
        } else {
            // 承認マスター名で検索
            //
            if( isset( $request->master_name )) {
                $masters = ApprovalMaster::where( 'name', 'like', '%'. $request->master_name .'%' )->get( 'id' )->toArray();
                $allocate_users = ApprovalMasterAllocate::whereIn( 'approval_master_id', $masters )->get( 'user_id' )->toArray();
                // dd( $masters, $allocate_users );
                $find['include_user_id'] = $allocate_users;
            }
            if( ! empty( $request->master_id )) {
                $allocate_users = ApprovalMasterAllocate::where( 'approval_master_id', $request->master_id )->get( 'user_id' )->toArray();
                $find['include_user_id'] = $allocate_users;
            }
            
        }
        // dd( $find );
        $users = User::getUserList( $find );

        // 承認マスターのデータ取得
        //
        $masters = array();        
        foreach( ApprovalMaster::all() as $col ) {
            $masters[$col->id] = $col->name;
        }
        
        // 承認マスター割当DBのデータ作成
        //
        $allocate = array();
        foreach( ApprovalMasterAllocate::all() as $col ) {
            $allocate[$col->user_id] = $masters[$col->approval_master_id];
        }
        
        //　戻るボタンの設定
        //
        BackButton::setHere( $request );
        
        return view( 'vacation.approvalMaster.selectUsers' )->with( 'request',  $request   )
                                                            ->with( 'find',     $find      )
                                                            ->with( 'allocate', $allocate  )
                                                            ->with( 'users',    $users     );
    }
    
    //  承認マスター割当　解除実行
    //
    public function deallocated( ApprovalMasterRequest $request ) {

        $users  = $request->input( 'users' );
        // dd( $request, $users );
        
        DB::transaction( function() use ( $users ) {
            DB::table( 'approval_master_allocates' )->whereIn( 'user_id', $users )->delete();
        // $a =    DB::table( 'approval_master_allocates' )->get();
            // dd( $users, $a );
            
        });

        Session::flash( 'flash_message', "承認マスターの割り当て解除しました" );
        session()->regenerateToken();
        // return redirect()->route( "vacation.approvalMaster.indexUsers" );
        return BackButton::backToThere();
    }
    
  
}
