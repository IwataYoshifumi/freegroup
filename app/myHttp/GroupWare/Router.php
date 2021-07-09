<?php

namespace App\myHttp\GroupWare;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Dept;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\Routes\ScreenSizeRoute;

class Router {
 
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // 　スケジュールルート
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    static public function route() {
        
        Route::middleware('auth:user')->prefix( 'groupware' )->name( 'groupware.' )->group(function () {
            
            // self::schedule_route();
            self::schedule2_route();
            // self::schedule_type_route();
            self::file_route();
            self::accesslist_route();
            self::group_route();
            self::calendar_route();
            self::calprop_route();
            
            self::tasklist_route();
            

            self::report_route();
            self::report_list_route();
            self::report_prop_route();
            
            self::tasklist_route();
            self::taskprop_route();
            self::task_route();
            
            self::show_all_route();

            self::test_route();            
            self::route_json();
        });

        Route::middleware('auth:admin')->group(function () {
            self::role_group_route();

            Route::prefix( 'groupware' )->name( 'groupware.' )->group( function() {
                self::route_init();
            });
        });
        
        Route::middleware( 'auth:user,admin' )->group( function() {
            self::route_ajax(); 
            self::test_user_admin_ok_route();
            ScreenSizeRoute::route();
        });
        
        self::customer_route();
        self::user_route();
        self::dept_route();
        
        self::exception_route();
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //    
    // モデル別、コントローラ別のルート
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //　Customer ルート（オーバーライド）
    //
    static public function customer_route() {
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->middleware( 'auth:user' )->group( function() {
            Route::get( '/customer/show/{customer}',   'CustomerController@detail'   )->name( 'customer.show' )->where( 'customer', '\d*+' );     
            Route::get( '/customer/detail/{customer}', 'CustomerController@detail' )->name( 'customer.detail' )->where( 'customer', '\d*+' );     
            Route::get( '/customer/detail/{customer}', 'CustomerController@detail' )->name( 'customer.detail' )->where( 'customer', '\d*+' );  
            
            Route::get(    '/customer/{customer}/delete',  'CustomerController@delete'     )->name('customer.delete' )->where( 'customer', '\d+' );
            Route::delete( '/customer/{customer}/delete',  'CustomerController@deleted'    )->name('customer.deleted')->where( 'customer', '\d+' );
        });
    }
    
    // User ルート（オーバーライド）
    //
    static public function user_route() {
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->name( 'groupware.user.' )->prefix( '/groupware/user/' )->group( function() {
            Route::middleware( 'auth:user,admin' )->group( function() {
                Route::get( 'home',          'UserController@mySelf' )->name( 'home' );
                Route::get( 'myself',        'UserController@mySelf' )->name( 'mySelf' );
                Route::get( 'detail/{user}', 'UserController@detail' )->name( 'detail' )->where( 'user', '\d*+' );     
            });
            
            Route::middleware( 'auth:user,admin' )->group( function() {
                Route::get(   '{user}',        'UserController@show'            )->name('show'  )->where( 'user', '\d+' );
                Route::get(   'index',         'UserController@index'           )->name('index' );
            });

            Route::middleware( 'auth:admin' )->group( function() {
                Route::get(   'create',        'UserController@create'  )->name('create');
                Route::post(  'create',         'UserController@store'  )->name('store' );
                Route::get(   '{user}/update', 'UserController@edit'    )->name('edit'   )->where( 'user', '\d+' );
                Route::post(  '{user}/update', 'UserController@update'  )->name('update' )->where( 'user', '\d+' );
                Route::get(    '{user}/delete',  'UserController@delete'     )->name('delete' )->where( 'user', '\d+' );
                Route::delete( '{user}/delete',  'UserController@deleted'    )->name('deleted' )->where( 'user', '\d+' );
            });
        });
        config(['groupware.user.index'    => '社員一覧',
                'groupware.user.create'   => '新規　社員登録',
                'groupware.user.store'    => '新規　社員登録完了',
                'groupware.user.show'     => '社員情報',
                'groupware.user.detail'   => '社員情報（詳細）',
                'groupware.user.edit'     => '社員情報　変更',
                'groupware.user.home'     => '社員ホーム',
                ]);
        

    }
    
    static public function dept_route() {
        // Deptモデル（部署モデル）
        
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::middleware([ 'auth:user,admin' ])->group(function () {
                Route::get( '/dept/index',          'DeptController@index'    )->name('dept.index'    );
                Route::get( '/dept/{dept}',         'DeptController@show'     )->name('dept.show'     )->where( 'dept', '[0-9]+' );
            });
            Route::middleware([ 'auth:admin' ])->group(function () {
                Route::get( '/dept/create',         'DeptController@create'   )->name('dept.create'   );
                Route::post('/dept/store',          'DeptController@store'    )->name('dept.store'    );
                Route::get( '/dept/{dept}/edit',    'DeptController@edit'     )->name('dept.edit'     )->where( 'dept', '[0-9]+' );
                Route::post( '/dept/{dept}',        'DeptController@update'   )->name('dept.update'   )->where( 'dept', '[0-9]+' );
                Route::get( '/dept/{dept}/destroy', 'DeptController@destroy'  )->name('dept.destory'  )->where( 'dept', '[0-9]+' );
                Route::post( '/dept/{dept}/destroy','DeptController@destroyed')->name('dept.destoryed')->where( 'dept', '[0-9]+' );
            });
        });
    
        config(['dept.index'    => '部署一覧',
                'dept.create'   => '新規　部署登録',
                'dept.store'    => '新規　部署登録完了',
                'dept.show'     => '部署情報',
                'dept.edit'     => '部署情報　変更',
                'dept.update'   => '部署情報　変更完了',
                'dept.destory'  => '部署　削除',
                'dest.destorid' => '部署　削除実行',
            ]);
    }
    
    //  Role系ルート
    //
    static public function role_group_route() {

        Route::prefix( 'groupware/role_group' )->name( 'groupware.role_group.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'RoleGroupController@index'  )->name( 'index'    );
            Route::get(  '/create',         'RoleGroupController@create' )->name( 'create'   );
            Route::post( '/create',         'RoleGroupController@store'  )->name( 'create'   );

            Route::get(  '/show/{role_group}',        'RoleGroupController@show'    )->name( 'show'   )->where( 'role_group', '\d+' );
            Route::get(  '/update/{role_group}',      'RoleGroupController@edit'    )->name( 'update' )->where( 'role_group', '\d+' );
            Route::post( '/update/{role_group}',      'RoleGroupController@update'  )->name( 'update' )->where( 'role_group', '\d+' );
            Route::get(  '/delete/{role_group}',      'RoleGroupController@delete'  )->name( 'delete' )->where( 'role_group', '\d+' );
            Route::delete(  '/delete/{role_group}',   'RoleGroupController@deleted' )->name( 'delete' )->where( 'role_group', '\d+' );

            Route::get(  '/select_users',   'RoleGroupController@select_users'  )->name( 'select_users' );
            Route::get(  '/attach_role',    'RoleGroupController@select_role'   )->name( 'attach_role'  );
            Route::post( '/attach_role',    'RoleGroupController@attach_role'   )->name( 'attach_role'  );
            
        });
        config([
            'groupware.role_group.index'          => 'ロールグループ一覧',
            'groupware.role_group.show'           => 'ロールグループ詳細',
            'groupware.role_group.create'         => 'ロールグループ新規作成',
            'groupware.role_group.update'         => 'ロールグループ修正',
            'groupware.role_group.delete'         => 'ロールグループ削除',

            'groupware.role_group.select_users'   => 'ロールグループ割当ユーザ選択',
            'groupware.role_group.attach_role'    => 'ロールグループ割当',
            
        ]);
        
    }
    
    //　Schedule2 ルート
    //
    static public function schedule2_route() {
        
        Route::prefix( 'schedule')->name( 'schedule.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            // Route::get(   '/index',      'Schedule2Controller@index'    )->name('index'       );
            // Route::get(   '/monthly',    'Schedule2Controller@monthly'  )->name('monthly'     );
            // Route::get(   '/weekly',     'Schedule2Controller@weekly'   )->name('weekly'      );
            // Route::get(   '/daily',      'Schedule2Controller@daily'    )->name('daily'       );

            // ScheduleIndexController
            //
            Route::get(   '/index',      'Schedule2IndexController@index'    )->name('index'       );
            Route::get(   '/monthly',    'Schedule2IndexController@monthly'  )->name('monthly'     );
            Route::get(   '/weekly',     'Schedule2IndexController@weekly'   )->name('weekly'      );
            Route::get(   '/daily',      'Schedule2IndexController@daily'    )->name('daily'       );
            Route::get(   '/show_modal/{schedule}',  'Schedule2IndexController@showModal' )->name('show_modal')->where( 'schedule', '\d+' );;

            Route::get(   '/csv',        'Schedule2Controller@csv'           )->name('csv'   );

            // ScheduleController
            //

            Route::get(   '/show/{schedule}',        'Schedule2Controller@show'      )->name('show'  )->where( 'schedule', '\d+' );
            Route::get(   '/show',                   'Schedule2Controller@show_m'    )->name('show_m');

            Route::get(   '/weekly_by_user',  'Schedule2Controller@weeklyByUser'    )->name('weekly_by_user'  );
            Route::get(   '/monthly_by_user', 'Schedule2Controller@monthlyByUser'   )->name('monthly_by_user' );

            Route::get(   '/json_search','Schedule2Controller@json_search'    )->name('json_search' );
            
            Route::get(   '/create',                'Schedule2Controller@create'    )->name('create' );
            Route::post(  '/create',                'Schedule2Controller@store'     )->name('store'  );
            Route::get(   '/edit/{schedule}',       'Schedule2Controller@edit'      )->name('edit'   )->where( 'schedule', '\d+' );
            Route::post(  '/edit/{schedule}',       'Schedule2Controller@update'    )->name('update' )->where( 'schedule', '\d+' );
            Route::get(     '/delete/{schedule}',   'Schedule2Controller@delete'    )->name('delete' )->where( 'schedule', '\d+' );
            Route::delete(  '/delete/{schedule}',   'Schedule2Controller@deleted'   )->name('deleted')->where( 'schedule', '\d+' );
            
            Route::get(   '/copy/{schedule}',       'Schedule2Controller@copy'      )->name('copy'   )->where( 'schedule', '\d+' );

            config(['groupware.schedule.index'    => '予定一覧',
            
                    'groupware.schedule.monthly'  => '月次表示',
                    'groupware.schedule.weekly'   => '週次表示',
                    'groupware.schedule.daily'    => '日次表示',
                    'groupware.schedule.create'   => '新規　予定登録',
                    'groupware.schedule.store'    => '新規　予定登録完了',
                    'groupware.schedule.show'     => '予定内容',
                    'groupware.schedule.detail'   => '予定詳細',
                    'groupware.schedule.edit'     => '予定　変更',
                    'groupware.schedule.copy'     => '新規　予定登録（複製）',
                    'groupware.schedule.update'   => '予定　変更完了',
                    'groupware.schedule.delete'   => '予定　削除',
                    'groupware.schedule.deleted'  => '予定　削除完了',
                    'groupware.schedule.select'   => '予定　選択',
                    ]);
        });
    }
    
    //  Report ルート
    //
    static public function report_route() {
        
        Route::prefix( 'report')->name( 'report.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
                
            Route::get(   '/index',      'ReportController@index'          )->name('index'   );
            Route::get(   '/csv',        'ReportController@csv'            )->name('csv'   );
            
            Route::get(   '/create',     'ReportController@create'          )->name('create');
            Route::post(  '/create',     'ReportController@store'           )->name('store' );
            
            Route::get(   '/show/{report}',     'ReportController@show'   )->name('show'  )->where( 'report', '\d+' );
            Route::get(   '/show',                'ReportController@show_m' )->name('show_m');
            
            Route::get(   '/edit/{report}',     'ReportController@edit'   )->name('edit'   )->where( 'report', '\d+' );
            Route::post(  '/edit/{report}',     'ReportController@update' )->name('update' )->where( 'report', '\d+' );

            Route::get(     '/delete/{report}',     'ReportController@delete'  )->name('delete' )->where( 'report', '\d+' );
            Route::delete(  '/delete/{report}',     'ReportController@deleted' )->name('deleted')->where( 'report', '\d+' );

            Route::get(  '/copy/{report}',     'ReportController@copy' )->name( 'copy' )->where( 'report', '\d+' );
            
            Route::get(   '/show_modal/{report}',  'ReportController@showModal' )->name('show_modal')->where( 'report', '\d+' );;

            config(['groupware.report.index'    => '日報一覧',
            
                    'groupware.report.create'   => '日報　新規作成',
                    'groupware.report.store'    => '日報　新規作成完了',
                    'groupware.report.show'     => '日報内容',
                    'groupware.report.detail'   => '日報詳細',
                    'groupware.report.edit'     => '日報　変更',
                    'groupware.report.update'   => '日報　変更完了',
                    'groupware.report.delete'   => '日報　削除',
                    'groupware.report.deleted'  => '日報　削除完了',
                    'groupware.report.select'   => '日報　選択',
                    ]);
        });
    
    }
    
    //  Report List ルート
    //
    static public function report_list_route() {
        Route::prefix( 'report_list/' )->name( 'report_list.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'ReportListController@index'  )->name('index'   );
            Route::get(  '/create',         'ReportListController@create' )->name( 'create'   );
            Route::post( '/create',         'ReportListController@store'  )->name( 'create'   );

            Route::get(  '/show/{report_list}',        'ReportListController@show'    )->name( 'show'   )->where( 'report_list', '\d+' );
            Route::get(  '/update/{report_list}',      'ReportListController@edit'    )->name( 'update' )->where( 'report_list', '\d+' );
            Route::post( '/update/{report_list}',      'ReportListController@update'  )->name( 'update' )->where( 'report_list', '\d+' );
            Route::get(  '/delete/{report_list}',      'ReportListController@delete'  )->name( 'delete' )->where( 'report_list', '\d+' );
            Route::delete(  '/delete/{report_list}',   'ReportListController@deleted' )->name( 'delete' )->where( 'report_list', '\d+' );

            config([
                'groupware.report_list.index'          => '日報リスト一覧',
                'groupware.report_list.show'           => '日報リスト管理者設定',
                'groupware.report_list.create'         => '日報リスト新規作成',
                'groupware.report_list.update'         => '日報リスト管理者設定修正',
                'groupware.report_list.delete'         => '日報リスト削除',
            ]);
        });
    }
    
    //  Report Prop ルート
    //
    static public function report_prop_route() {
        Route::prefix( 'report_prop/' )->name( 'report_prop.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',                 'ReportPropController@index'  )->name('index'   );

            Route::get(  '/show/{report_prop}',        'ReportPropController@show'    )->name( 'show'   )->where( 'report_prop', '\d+' );
            Route::get(  '/update/{report_prop}',      'ReportPropController@edit'    )->name( 'update' )->where( 'report_prop', '\d+' );
            Route::post( '/update/{report_prop}',      'ReportPropController@update'  )->name( 'update' )->where( 'report_prop', '\d+' );

            config([
                'groupware.report_prop.index'          => '【個人設定】日報リスト設定　一覧',
                'groupware.report_prop.show'           => '【個人設定】日報リスト設定',
                'groupware.report_prop.update'         => '【個人設定】日報リスト設定　変更',
            ]);
        });
    }
    
    //  File ルート
    //
    static public function file_route() {
        Route::prefix( 'file' )->name( 'file.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            
            Route::get(   '/index',             'FileController@index'          )->name('index'   );
            Route::get(   '/show/{file}',       'FileController@show'           )->name('show'    )->where( 'file', '\d+' );
            Route::get(   '/detail/{file}',     'FileController@detail'         )->name('detail'  )->where( 'file', '\d+' );
            Route::get(   '/select',            'FileController@select'    )->name('select'  );
            Route::get(   '/delete',            'FileController@delete'    )->name('delete'  );
            Route::delete('/delete',            'FileController@deleted'   )->name('deleted' );

            Route::get(   '/download/myfile/{file}',   'FileController@downloadMyFile' )->name('downloadMyFile')
                                                                                        ->where( 'file',  '\d+' );
            Route::get(   '/download/{file}/{class}/{model}',   'FileController@download' )->name('download')
                                                                                           ->where( 'file',  '\d+' )
                                                                                           ->where( 'model', '\d+' )
                                                                                           ->where( 'class', '\w+' );
            Route::get(   '/viewInBrowser/{file}/{class}/{model}',   'FileController@viewInBrowser' )->name('view')
                                                                                           ->where( 'file',  '\d+' )
                                                                                           ->where( 'model', '\d+' )
                                                                                           ->where( 'class', '\w+' );

            Route::get( '/json/file_search',   'FileController@json_search'  )->name('json_search');
            
            Route::post(   '/api/upload', 'FileController@uploadAPI' )->name( 'api.upload' );
            Route::post(   '/api/delete', 'FileController@deleteAPI' )->name( 'api.delete' );

            if( 1 or is_debug() ) {

                Route::get( '/deleteAllUntachedFiles',  'FileController@deleteAllUntachedFiles'   )->name('deleteAllUntachedFiles');
            }

            config(['groupware.file.index'    => 'ファイル一覧',
                    'groupware.file.show'     => 'ファイル内容',
                    'groupware.file.detail'   => 'ファイル詳細',
                    'groupware.file.select'   => 'ファイル選択削除',
                    'groupware.file.delete'   => 'ファイル削除（確認）',
                    'groupware.file.deleted'  => 'ファイル　削除完了',
                    ]);
        });
    }
    
    //  AccessList ルート
    //
    static public function accesslist_route() {
        Route::prefix( 'accesslist/' )->name( 'access_list.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'AccessListController@index'          )->name('index'   );
            Route::get(  '/create',         'AccessListController@create' )->name( 'create'   );
            Route::post( '/create',         'AccessListController@store'  )->name( 'create'   );

            Route::get(  '/show/{access_list}',        'AccessListController@show'    )->name( 'show'   )->where( 'access_list', '\d+' );
            Route::get(  '/update/{access_list}',      'AccessListController@edit'    )->name( 'update' )->where( 'access_list', '\d+' );
            Route::post( '/update/{access_list}',      'AccessListController@update'  )->name( 'update' )->where( 'access_list', '\d+' );
            Route::get(  '/delete/{access_list}',      'AccessListController@delete'  )->name( 'delete' )->where( 'access_list', '\d+' );
            Route::delete(  '/delete/{access_list}',   'AccessListController@deleted' )->name( 'delete' )->where( 'access_list', '\d+' );

            config([
                'groupware.access_list.index'          => 'アクセスリスト一覧',
                'groupware.access_list.show'           => 'アクセスリスト詳細',
                'groupware.access_list.create'         => 'アクセスリスト新規作成',
                'groupware.access_list.update'         => 'アクセスリスト修正',
                'groupware.access_list.delete'         => 'アクセスリスト削除',
            ]);
        });
    }
    
    //  Group ルート
    //
    static public function group_route() {
        Route::prefix( 'group/' )->name( 'group.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'GroupController@index'          )->name('index'   );
            Route::get(  '/create',         'GroupController@create' )->name( 'create'   );
            Route::post( '/create',         'GroupController@store'  )->name( 'create'   );

            Route::get(  '/show/{group}',        'GroupController@show'    )->name( 'show'   )->where( 'group', '\d+' );
            Route::get(  '/update/{group}',      'GroupController@edit'    )->name( 'update' )->where( 'group', '\d+' );
            Route::post( '/update/{group}',      'GroupController@update'  )->name( 'update' )->where( 'group', '\d+' );
            Route::get(  '/delete/{group}',      'GroupController@delete'  )->name( 'delete' )->where( 'group', '\d+' );
            Route::delete(  '/delete/{group}',   'GroupController@deleted' )->name( 'delete' )->where( 'group', '\d+' );

            config([
                'groupware.group.index'          => 'グループ一覧',
                'groupware.group.show'           => 'グループ詳細',
                'groupware.group.create'         => 'グループ新規作成',
                'groupware.group.update'         => 'グループ修正',
                'groupware.group.delete'         => 'グループ削除',
            ]);
        });
    }
    
    //  Calendar ルート
    //
    static public function calendar_route() {
        Route::prefix( 'calendar/' )->name( 'calendar.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'CalendarController@index'  )->name('index'   );
            Route::get(  '/create',         'CalendarController@create' )->name( 'create'   );
            Route::post( '/create',         'CalendarController@store'  )->name( 'create'   );

            Route::get(  '/show/{calendar}',        'CalendarController@show'    )->name( 'show'   )->where( 'calendar', '\d+' );
            Route::get(  '/update/{calendar}',      'CalendarController@edit'    )->name( 'update' )->where( 'calendar', '\d+' );
            Route::post( '/update/{calendar}',      'CalendarController@update'  )->name( 'update' )->where( 'calendar', '\d+' );
            Route::get(  '/delete/{calendar}',      'CalendarController@delete'  )->name( 'delete' )->where( 'calendar', '\d+' );
            Route::delete(  '/delete/{calendar}',   'CalendarController@deleted' )->name( 'delete' )->where( 'calendar', '\d+' );

            config([
                'groupware.calendar.index'          => 'カレンダー一覧',
                'groupware.calendar.show'           => 'カレンダー管理者設定',
                'groupware.calendar.create'         => 'カレンダー新規作成',
                'groupware.calendar.update'         => 'カレンダー管理者設定修正',
                'groupware.calendar.delete'         => 'カレンダー削除',
            ]);
        });
    }
    
        //  CalProp ルート
    //
    static public function calprop_route() {
        Route::prefix( 'calprop/' )->name( 'calprop.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',                 'CalPropController@index'  )->name('index'   );

            Route::get(  '/show/{calprop}',        'CalPropController@show'    )->name( 'show'   )->where( 'calprop', '\d+' );
            Route::get(  '/update/{calprop}',      'CalPropController@edit'    )->name( 'update' )->where( 'calprop', '\d+' );
            Route::post( '/update/{calprop}',      'CalPropController@update'  )->name( 'update' )->where( 'calprop', '\d+' );

            Route::get(  '/gsync_all',             'CalPropController@gsyncAll'   )->name( 'gsync_all'   );
            Route::get(  '/gsync/{calprop}',       'CalPropController@gsync'      )->name( 'gsync'       )->where( 'calprop', '\d+' );
            Route::get(  '/gsync_on/{calprop}',    'CalPropController@gsyncOn'    )->name( 'gsync_on'    )->where( 'calprop', '\d+' );
            Route::get(  '/gsync_check/{calprop}', 'CalPropController@gsyncCheck' )->name( 'gsync_check' )->where( 'calprop', '\d+' );

            config([
                'groupware.calprop.index'          => '【個人設定】カレンダー表示・初期値・Google同期設定　一覧',
                'groupware.calprop.show'           => '【個人設定】カレンダー表示・初期値・Google同期設定',
                'groupware.calprop.update'         => '【個人設定】カレンダー表示・初期値・Google同期設定　変更',
                'groupware.calprop.gsync_all'      => 'カレンダーGoogle手動全同期',
                'groupware.calprop.gsync'          => 'カレンダーGoogle手動同期',
                'groupware.calprop.gsync_check'    => 'カレンダーGoogle同期チェック',
            ]);
        });
    }
    
    
    
    
    //   TaskListルート
    //
    static public function tasklist_route() {
        Route::prefix( 'tasklist/' )->name( 'tasklist.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',          'TaskListController@index'  )->name('index'   );
            Route::get(  '/create',         'TaskListController@create' )->name( 'create'   );
            Route::post( '/create',         'TaskListController@store'  )->name( 'create'   );

            Route::get(  '/show/{tasklist}',        'TaskListController@show'    )->name( 'show'   )->where( 'tasklist', '\d+' );
            Route::get(  '/update/{tasklist}',      'TaskListController@edit'    )->name( 'update' )->where( 'tasklist', '\d+' );
            Route::post( '/update/{tasklist}',      'TaskListController@update'  )->name( 'update' )->where( 'tasklist', '\d+' );
            Route::get(  '/delete/{tasklist}',      'TaskListController@delete'  )->name( 'delete' )->where( 'tasklist', '\d+' );
            Route::delete(  '/delete/{tasklist}',   'TaskListController@deleted' )->name( 'delete' )->where( 'tasklist', '\d+' );
            


            config([
                'groupware.tasklist.index'          => 'タスクリスト一覧',
                'groupware.tasklist.show'           => 'タスクリスト管理者設定',
                'groupware.tasklist.create'         => 'タスクリスト新規作成',
                'groupware.tasklist.update'         => 'タスクリスト管理者設定修正',
                'groupware.tasklist.delete'         => 'タスクリスト削除',
            ]);
        });
    }
    
    //  TaskProp ルート
    //
    static public function taskprop_route() {
        Route::prefix( 'taskprop/' )->name( 'taskprop.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(  '/index',                 'TaskPropController@index'  )->name('index'   );

            Route::get(  '/show/{taskprop}',        'TaskPropController@show'    )->name( 'show'   )->where( 'taskprop', '\d+' );
            Route::get(  '/update/{taskprop}',      'TaskPropController@edit'    )->name( 'update' )->where( 'taskprop', '\d+' );
            Route::post( '/update/{taskprop}',      'TaskPropController@update'  )->name( 'update' )->where( 'taskprop', '\d+' );

            config([
                'groupware.taskprop.index'          => '【個人設定】タスク表示設定　一覧',
                'groupware.taskprop.show'           => '【個人設定】タスク表示設定',
                'groupware.taskprop.update'         => '【個人設定】タスク表示設定　変更',
            ]);
        });
    }
    
    //  Task ルート
    //
    static public function task_route() {
        
        Route::prefix( 'task')->name( 'task.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
                
            Route::get(   '/index',      'TaskController@index'          )->name('index'   );
            Route::get(   '/csv',        'TaskController@csv'            )->name('csv'   );
            
            Route::get(   '/create',     'TaskController@create'          )->name('create');
            Route::post(  '/create',     'TaskController@store'           )->name('store' );
            
            Route::get(   '/show/{task}',     'TaskController@show'   )->name('show'  )->where( 'task', '\d+' );
            Route::get(   '/show',                'TaskController@show_m' )->name('show_m');
            
            Route::get(   '/edit/{task}',     'TaskController@edit'   )->name('edit'   )->where( 'task', '\d+' );
            Route::post(  '/edit/{task}',     'TaskController@update' )->name('update' )->where( 'task', '\d+' );

            Route::get(     '/delete/{task}',     'TaskController@delete'  )->name('delete' )->where( 'task', '\d+' );
            Route::delete(  '/delete/{task}',     'TaskController@deleted' )->name('deleted')->where( 'task', '\d+' );

            Route::get(  '/copy/{task}',     'TaskController@copy' )->name( 'copy' )->where( 'task', '\d+' );
            
            Route::post(  '/complete/{task}', 'TaskController@complete' )->name( 'complete' )->where( 'task', '\d+' );
            
            Route::get(   '/show_modal/{task}',  'TaskController@showModal' )->name('show_modal')->where( 'task', '\d+' );;


            config(['groupware.task.index'    => 'タスク一覧',
            
                    'groupware.task.create'   => 'タスク　新規作成',
                    'groupware.task.store'    => 'タスク　新規作成完了',
                    'groupware.task.show'     => 'タスク内容',
                    'groupware.task.detail'   => 'タスク詳細',
                    'groupware.task.edit'     => 'タスク　変更',
                    'groupware.task.update'   => 'タスク　変更完了',
                    'groupware.task.delete'   => 'タスク　削除',
                    'groupware.task.deleted'  => 'タスク　削除完了',
                    'groupware.task.select'   => 'タスク　選択',
                    'groupware.task.show_modal'   => 'タスク詳細',
                    ]);
        });
    
    }

    //　スケジュール・タスク一覧表示（月次・週次・日次表示）
    //
    static public function show_all_route() {
        Route::prefix( 'show_all')->name( 'show_all.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::get( '/monthly', 'ShowAllController@monthly' )->name( 'monthly' );
            Route::get( '/weekly',  'ShowAllController@weekly'  )->name( 'weekly' );
            Route::get( '/daily',   'ShowAllController@daily'   )->name( 'daily' );
            Route::get( '/dialog/daily',   'ShowAllController@dailyDiallog'   )->name( 'dialog.daily' );
            Route::get( '/index',               'ShowAllController@index'            )->name( 'index' );
            Route::get( '/index/exec_search',   'ShowAllController@indexExecSearch'  )->name( 'indexEexecSearch' );
            
            
        });
        
        config(['groupware.show_all.monthly'    => 'スケジュール・タスク　月次表示',
                'groupware.show_all.weekly'     => 'スケジュール・タスク　週次表示',
                'groupware.show_all.daily'     => 'スケジュール・タスク　日次表示',
                'groupware.show_all.index'      => 'スケジュール・タスク・日報　検索',
                'groupware.show_all.indexExecSearch'  => 'スケジュール・タスク・日報　検索',
                ]);
    }
    



    //　初期化ルート
    //
    static public function route_init() {
        Route::prefix( 'init/' )->name( 'init.' )->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::get(  '/all_users', 'InitController@showForminitAllUsers' )->name( 'all_users' );
            Route::post( '/all_users', 'InitController@initAllUsers'         )->name( 'all_users' );
            config([
                'groupware.init.all_users'          => 'DB初期化',
            ]);
            
        });
    }


    
    //  開発用ルート（ TEST　）
    //
    static public function test_route() {
        Route::name( 'test.' )->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {

            //　File Component 開発用
            //
            if( true or is_debug() ) {
                
                Route::get( '/test',     'TestController@test'     )->name( 'test' );
                Route::get( '/delete_files', 'TestController@deleteFiles' )->name( 'delete_files' );
                Route::get(  '/files',  'TestController@files'        )->name('files');
                Route::post( '/files',  'TestController@filesUpdate'  )->name('files');
                Route::get( '/search_report_lists', 'TestController@searchReportLists' )->name( 'search_report_lists');
            }

            config([
                    'groupware.test.files'     => 'ファイルテスト',
                    'groupware.test.search_report_lists'     => '日報リスト検索',
            ]);
        });
    }
    
    static public function test_user_admin_ok_route() {
        Route::name( 'groupware.test.' )->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {

            //　File Component 開発用
            //
            if( true or is_debug() ) {
                
                //　テンプレートルートをコピーして使う
                //
                Route::get( '/template', 'TestController@template' )->name( 'template' );
                Route::get( '/depts_users_customers', 'TestController@testDeptUserCustomer' )->name( 'depts_users_customers' );
                
                //　スクリーンサイズ取得テスト
                //
                
                
                //　カスタムBlade iconのテスト
                //
                Route::get( '/custome_blade_icons',  'TestController@icons'    )->name( 'custome_blade_icons' );
            }

            config([
                    'groupware.test.files'     => 'ファイルテスト',
            ]);
        });
        
        
    }
    

    
    //  JSONルート AJAX
    // 
    static public function route_json() {
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::get('/json/getUsers',            'JsonController@getUsers'  )->name( 'json.getUsers'  );
            Route::get('/json/getUsersBlongsTo',    'JsonController@getUsersBlongsTo'  )->name( 'json.getUsersBlongsTo'  );
            Route::get('/json/getApprovalMaster',   'JsonController@getApprovalMaster' )->name( 'json.getApprovalMaster' );
        });
    }
    
    static public function route_ajax() {
        Route::namespace( '\App\myHttp\GroupWare\Controllers\AJAX' )->group( function() {
            
            Route::get('/ajax/dept/search',         'DeptController@search'         )->name( 'ajax.dept.search'     );
            Route::get('/ajax/user/search',         'UserController@search'         )->name( 'ajax.user.search'     );
            Route::get('/ajax/customer/search',     'CustomerController@search'     )->name( 'ajax.customer.search' );
            Route::get('/ajax/report_list/search',   'ReportListController@search'   )->name( 'ajax.report_list.search' );
            Route::get('/ajax/report_list/search2',  'ReportListController@searchForCheckboxes' )->name( 'ajax.report_list.search2' );

            
            Route::get( '/ajax/tasklist/search', 'TaskListController@search' )->name( 'ajax.tasklist.search' );
            
            Route::get( '/ajax/calendar/search', 'CalendarController@search' )->name( 'ajax.calendar.search' );
            
            
            
        });
    }
    
    



    //  例外処理関係のルート
    //
    static public function exception_route() {
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::get( '/groupware/noauth',    'ExceptionController@noAuthRoute' )->name( 'groupware.noarth'  );
        });
            
    }
}