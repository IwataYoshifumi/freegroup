<?php

// namespace App\Http\Controllers\Vacation;
namespace App\MyApps\Vacation;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\MyApps\Vacation\Models\User;
use App\MyApps\Vacation\Models\Dept;
use App\MyApps\Vacation\Models\Application;
use App\MyApps\Vacation\Models\Approval;
use App\MyApps\Vacation\Models\Vacation;
use App\MyApps\Vacation\Models\VacationList;
use App\MyApps\Vacation\Models\ApprovalMaster;
use App\MyApps\Vacation\Models\ApprovalMasterAllocate;

use App\MyApps\Vacation\Controllers\UserController;
use App\MyApps\Vacation\Controllers\JsonController;

use App\Http\Helpers\BackButton;

// class Router {
class VacationRouter {
 
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 有給休暇申請システム
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////

    static public function route() {

        Route::namespace( 'Vacation' )->prefix( '/vacation' )->name( 'vacation.' )->group(function () { 
        
                // 休暇申請（Application DB, Approval DB）
                // 
                Route::middleware(['auth:user'])->group( function() {
                        Route::get(  '/application/index',  'ApplicationController@index'  )->name('application.index'  );
                        Route::get(  '/application/create',         'ApplicationController@create'   )->name('application.create'   );
                        Route::post( '/application/store',          'ApplicationController@store'    )->name('application.store'    );
                        Route::get(  '/application/create_hourly',   'ApplicationController@create_hourly' )->name('application.create_hourly'   );
                        Route::post( '/application/store_hourly',    'ApplicationController@store_hourly'  )->name('application.store_hourly'    );
                });
                Route::middleware( ['access_application'] )->group( function() {
                        Route::get(  '/application/{application}/show/',   'ApplicationController@show'     )->name('application.show'     )->where( 'application', '[0-9]+');
                        Route::get(  '/application/{application}/process/','ApplicationController@process'  )->name('application.process'  )->where( 'application', '[0-9]+');
                        Route::post( '/application/{application}/process/','ApplicationController@processed')->name('application.processed')->where( 'application', '[0-9]+');
                        Route::get(  '/application/{application}/drop/',   'ApplicationController@drop'     )->name('application.drop'     )->where( 'application', '[0-9]+');
                        Route::post( '/application/{application}/drop/',   'ApplicationController@dropped'  )->name('application.dropped'  )->where( 'application', '[0-9]+');
                });
                
                //　承認済み休暇の休暇取得完了済みかの確認
                //
                Route::middleware( ['auth:admin'] )->group( function() {
                        // dd( 'ad');
                        Route::get( '/application/checkProsessed',    'ApplicationController@checkProcessed'    )->name( 'application.checkProcessed'    );
                        Route::get( '/application/notifyIncomplited', 'ApplicationController@notifyIncompleted' )->name( 'application.notifyIncompleted' );
                });
                config(['vacation.application.create'      => '休暇申請',
                        'vacation.application.create_hourly'=> '時間有給申請',
                        'vacation.application.store'       => '休暇申請　完了',
                        'vacation.application.show'        => '休暇申請　情報',
                        'vacation.application.index'       => '休暇申請　一覧',
                        'vacation.application.process'     => '休暇取得　完了処理',
                        'vacation.application.processed'   => '休暇取得　完了',
                        'vacation.application.drop'        => '休暇申請　取消',
                        'vacation.application.droped'      => '休暇申請　取消完了',
                        'vacation.application.checkProcessed' => '休暇完了処理チェック',
                        ]);
                
                //　休暇承認
                //
                Route::middleware([ 'auth:user' ])->group( function() {
                        Route::get( '/approval/index',  'ApprovalController@index'  )->name( 'approval.index' );
                        Route::get( '/approval/select', 'ApprovalController@select' )->name( 'approval.select' );
                });
        
                //　申請者・承認者がアクセス可能
                //
                Route::middleware( ['access_approval'] )->group( function() {
                        Route::get(  '/approval/{approval}',            'ApprovalController@show'       )->name( 'approval.show'     )->where( 'approval', '[0-9]+' );
                        Route::get(  '/approval/{approval}/approve',    'ApprovalController@approve'    )->name( 'approval.approve'  )->where( 'approval', '[0-9]+' );
                        Route::post( '/approval/{approval}/approved',   'ApprovalController@approved'   )->name( 'approval.approved' )->where( 'approval', '[0-9]+' );
                        Route::get(  '/approval/{approval}/reject',     'ApprovalController@reject'     )->name( 'approval.reject'   )->where( 'approval', '[0-9]+' );
                        Route::post( '/approval/{approval}/rejected',   'ApprovalController@rejected'   )->name( 'approval.rejected' )->where( 'approval', '[0-9]+' );
                });
                config(['vaction.approval.index'              => '承認一覧',
                        'vaction.approval.select'             => '承認業務（休暇申請選択）',
                        'vaction.approval.show'               => '承認業務内容',
                        'vaction.approval.approve'            => '承認（確認）',
                        'vaction.approval.approved'           => '承認（完了）',
                        'vaction.approval.reject'             => '休暇申請却下（確認）',
                        'vaction.approval.rejected'           => '休暇申請却下（完了）',
                        ]);
                
                //　休暇検索
                //
                Route::middleware( ['is_login'] )->group( function() {
                    Route::get( '/common/vacation',    'CommonController@vacation'   )->name( 'common.vacation' );
                    Route::get( '/common/vindex',      'CommonController@vindex'     )->name( 'common.vindex'   );     // 休暇検索
                    Route::get( '/common/no_vacation', 'CommonController@noVacation' )->name( 'common.no_vacation' );  // 有給未取得者検索
                    Route::get( '/common/how_many_days_left_for_paidleave', 
                                    'CommonController@howManyDaysLeftForPaidleave' )
                                    ->name( 'common.how_many_days_left_for_paidleave' );  // 有給休暇残日数
                    Route::get( '/common/csv',               'CommonController@csv'            )->name( 'common.csv'      );  
                    Route::get( '/common/csv/no_vacation',   'CommonController@noVacationCSV'  )->name( 'common.no_vacation.csv' );
                    Route::get( '/common/csv/how_many_days', 'CommonController@howManyDaysCSV' )->name( 'common.how_many_day.csv' );
                });
                config(['vacation.common.vacation'          => '休暇取得状況',
                        'vacation.common.vindex'            => '休暇検索',
                        'vacation.common.no_vacation'       => '有給　未取得者 検索',
                        'vacation.common.how_many_days_left_for_paidleave' => '有給休暇　残日数検索',
                        ]);
                        
                //　ユーザルート
                //
                Route::middleware([ 'auth:admin' ])->group(function () {
                        Route::get( '/user/index',      'UserController@index'           )->name('user.index' );
                        Route::get( '/user/create',     'UserController@create'          )->name('user.create');
                        Route::post('/user/store',      'UserController@store'           )->name('user.store' );
                        Route::get( '/user/{user}/edit', 'UserController@edit'           )->name('user.edit'  )->where( 'user', '[0-9]+' );
                        Route::post( '/user/{user}',    'UserController@update'          )->name('user.update')->where( 'user', '[0-9]+' );
                });
        
                //
                // 従業員情報の閲覧は閲覧権限のある人だけ
                Route::middleware( ['has_browsing_rights'] )->group( function() {
                        Route::get( '/user/{user}',     'UserController@show'            )->name('user.show'  )->where('user', '[0-9]+' );
                        Route::get( '/user/detail/{user}', 'UserController@detail'       )->name('user.detail')->where('user', '[0-9]+' );
                    });
                Route::middleware( ['auth:user'])->group( function() {
                        Route::get( '/user/detail',        'UserController@detail_mySelf'  )->name('user.detail_mySelf');
                });
                config(['vacation.user.index'    => '社員一覧',
                        'vacation.user.create'   => '新規　社員登録',
                        'vacation.user.store'    => '新規　社員登録完了',
                        'vacation.user.show'     => '社員情報',
                        'vacation.user.detail'   => '社員情報（詳細）',
                        'vacation.user.edit'     => '社員情報　変更',
                        'vacation.user.detail_mySelf'   => '自分情報 詳細',
                        ]);
                        
                //  有給割当、休暇データ関連
                //
                Route::middleware( ['auth:admin'] )->group( function() {
                        Route::get( '/allocate/select',         'VacationController@select'    )->name( 'allocate.select' );
                        Route::get( '/allocate/create',         'VacationController@create'    )->name( 'allocate.create' );
                        Route::post('/allocate/store',          'VacationController@store'     )->name( 'allocate.store'  );
                        Route::get( '/vacation/index',         'VacationController@index'      )->name( 'vacation.index' );
                        Route::get( '/vacation/{vacation}/edit', 'VacationController@edit'     )->name( 'vacation.edit'  )->where( 'vacation', '[0-9]+' );
                        Route::post( '/vacation/{vacation}',   'VacationController@update'     )->name( 'vacation.update')->where( 'vacation', '[0-9]+' );
                });
                Route::middleware( ['has_browsing_rights' ] )->group( function() {
                        Route::get( '/vacation/{vacation}',   'VacationController@show'        )->name( 'vacation.show'  )->where( 'vacation', '[0-9]+' );
                        Route::get( '/paidleave/{vacation}',  'VacationController@show'        )->name( 'paidleave.show' )->where( 'vacation','[0-9]+' );
                });
                config(['vacation.allocate.select'          => '有給割当（割当社員選択）',
                        'vacation.allocate.create'          => '有給割当',
                        'vacation.allocate.store'           => '有給割当（完了）',
                        'vacation.vacation.index'          => '有給割当状況　一覧',
                        'vacation.vacation.show'           => '有給割当情報　詳細',
                        'vacation.paidleave.show'          => '有給割当情報　詳細',
                        'vacation.vacation.edit'           => '有給割当（修正）',
                        'vacation.vacation.update'         => '有給割当（修正完了）',
                        ]);
                
                //  部署管理
                //
                Route::middleware( ['auth:admin'] )->group( function() {
                    VacationRouter::route_dept();
                });    
                //  承認マスター
                //
                Route::middleware( ['auth:admin'] )->group( function() {
                    VacationRouter::route_approvalMaster();
                });    
                //  JSON
                //
                Route::middleware( ['is_login'] )->group( function() {
                    VacationRouter::route_json();
                });

        });
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //    
    // モデル別、コントローラ別のルート
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //　休暇検索ルート
    //
    static public function route_common() {
    
        //　休暇検索
        //
        Route::get( '/common/vacation',    'CommonController@vacation'   )->name( 'common.vacation' );
        Route::get( '/common/vindex',      'CommonController@vindex'     )->name( 'common.vindex'   );     // 休暇検索
        Route::get( '/common/no_vacation', 'CommonController@noVacation' )->name( 'common.no_vacation' );  // 有給未取得者検索
        Route::get( '/common/how_many_days_left_for_paidleave', 
                        'CommonController@howManyDaysLeftForPaidleave' )
                        ->name( 'common.how_many_days_left_for_paidleave' );  // 有給休暇残日数

        Route::get( '/common/csv',               'CommonController@csv'            )->name( 'common.csv'      );  
        Route::get( '/common/csv/no_vacation',   'CommonController@noVacationCSV'  )->name( 'common.no_vacation.csv' );
        Route::get( '/common/csv/how_many_days', 'CommonController@howManyDaysCSV' )->name( 'common.how_many_day.csv' );

        
        config(['vacation.common.vacation'          => '休暇取得状況',
                'vacation.common.vindex'            => '休暇検索',
                'vacation.common.no_vacation'       => '有給　未取得者 検索',
                'vacation.common.how_many_days_left_for_paidleave' => '有給休暇　残日数検索',
                ]);
    
    }
    
    //  申請マスタールート
    //
    static public function route_approvalMaster() {
            
        //　申請マスター
        //
        // Route::middleware([ 'auth:user' ])->group(function () {
                Route::get(  '/approvalMaster/create',            'ApprovalMasterController@create'     )->name( 'approvalMaster.create'     );
                Route::post( '/approvalMaster/store',             'ApprovalMasterController@store'      )->name( 'approvalMaster.store'      );
                Route::get(  '/approvalMaster/index',             'ApprovalMasterController@index'      )->name( 'approvalMaster.index'      );
                Route::get(  '/approvalMaster/{master}',          'ApprovalMasterController@show'       )->name( 'approvalMaster.show'       )->where( 'master', '[0-9]+');
                Route::get(  '/approvalMaster/{master}/edit',     'ApprovalMasterController@edit'       )->name( 'approvalMaster.edit'       )->where( 'master', '[0-9]+');
                Route::post( '/approvalMaster/{master}/update',   'ApprovalMasterController@update'     )->name( 'approvalMaster.update'     )->where( 'master', '[0-9]+');
                Route::get(  '/approvalMaster/indexUsers',        'ApprovalMasterController@indexUsers' )->name( 'approvalMaster.indexUsers' );
                Route::get(  '/approvalMaster/selectUsers',       'ApprovalMasterController@selectUsers')->name( 'approvalMaster.selectUsers');
                Route::get(  '/approvalMaster/allocate',          'ApprovalMasterController@allocate'   )->name( 'approvalMaster.allocate'   );
                Route::post( '/approvalMaster/allocated',         'ApprovalMasterController@allocated'  )->name( 'approvalMaster.allocated'  );
                
                Route::get(  '/approvalMaster/deallocate/selectUsers', 'ApprovalMasterController@deallocateSelectUsers')->name( 'approvalMaster.deallocateSelectUsers');
                Route::get(  '/approvalMaster/deallocate',             'ApprovalMasterController@deallocate'           )->name( 'approvalMaster.deallocate'    );
                Route::post( '/approvalMaster/deallocate',             'ApprovalMasterController@deallocated'          )->name( 'approvalMaster.deallocated'   );

        config(['vacation.approvalMaster.create'     => '申請マスター（新規作成）',
                'vacation.approvalMaster.index'      => '申請マスター（一覧）',
                'vacation.approvalMaster.indexUsers' => '申請マスター（割当状況）',
                'vacation.approvalMaster.show'       => '申請マスター（詳細）',
                'vacation.approvalMaster.edit'       => '申請マスター（変更）',
                'vacation.approvalMaster.update'     => '申請マスター（変更完了）',
                'vacation.approvalMaster.selectUsers'=> '申請マスター（割当社員選択）',
                'vacation.approvalMaster.allocate'   => '申請マスター登録',
                'vacation.approvalMaster.deallocateSelectUsers' => "申請マスター割当解除",
                'vacation.approvalMaster.deallocated'=> '申請マスター割当解除'
                ]);
        // });
    }
    
    //　部署ＤＢのルート
    //
    static public function route_dept() {
        // Route::middleware([ 'adminAuth' ])->group(function () {
                Route::get( '/dept/index',          'DeptController@index'    )->name('dept.index'    );
                Route::get( '/dept/create',         'DeptController@create'   )->name('dept.create'   );
                Route::post('/dept/store',          'DeptController@store'    )->name('dept.store'    );
                Route::get( '/dept/{dept}',         'DeptController@show'     )->name('dept.show'     )->where( 'dept', '[0-9]+' );
                Route::get( '/dept/{dept}/edit',    'DeptController@edit'     )->name('dept.edit'     )->where( 'dept', '[0-9]+' );
                Route::post( '/dept/{dept}',        'DeptController@update'   )->name('dept.update'   )->where( 'dept', '[0-9]+' );
                Route::get( '/dept/{dept}/destroy', 'DeptController@destroy'  )->name('dept.destory'  )->where( 'dept', '[0-9]+' );
                Route::post( '/dept/{dept}/destroy','DeptController@destroyed')->name('dept.destoryed')->where( 'dept', '[0-9]+' );

        config(['vacation.dept.index'    => '部署一覧',
                'vacation.dept.create'   => '新規　部署登録',
                'vacation.dept.store'    => '新規　部署登録完了',
                'vacation.dept.show'     => '部署情報',
                'vacation.dept.edit'     => '部署情報　変更',
                'vacation.dept.update'   => '部署情報　変更完了',
                'vacation.dept.destory'  => '部署　削除',
                'vacation.dest.destorid' => '部署　削除実行',
                ]);

        // });
    }
    
    //  JSONルート
    // 
    static public function route_json() {
            Route::get('/json/getUsersBlongsTo',    'JsonController@getUsersBlongsTo'  )->name( 'json.getUsersBlongsTo'  );
            Route::get('/json/getApprovalMaster',   'JsonController@getApprovalMaster' )->name( 'json.getApprovalMaster' );
    }
    
            
            
}