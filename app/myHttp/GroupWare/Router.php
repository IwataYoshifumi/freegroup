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

class Router {
 
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // 　スケジュールルート
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    static public function route() {
        
        Route::middleware('auth:user')->prefix( 'groupware' )->name( 'groupware.' )->group(function () {
            self::schedule_route();
            self::schedule_type_route();
            self::report_route();
            self::file_route();
        });
        
        self::customer_route();
        self::user_route();
        
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
        Route::namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            Route::middleware( 'auth:user' )->group( function() {
                Route::get( '/user/home',          'UserController@mySelf' )->name( 'user.home' );
                Route::get( '/user/myself',        'UserController@mySelf' )->name( 'user.mySelf' );
                Route::get( '/user/detail/{user}', 'UserController@detail' )->name( 'user.detail' )->where( 'user', '\d*+' );     
            });
            
            Route::middleware( 'auth:admin' )->group( function() {
                Route::get(    '/user/{user}/delete',  'UserController@delete'     )->name('user.delete' )->where( 'user', '\d+' );
                Route::delete( '/user/{user}/delete',  'UserController@deleted'    )->name('user.deleted')->where( 'user', '\d+' );
            });
        });
        config( ['user.home' => '社員ホーム画面' ] );
        

    }
    
    //　Schedule ルート
    //
    static public function schedule_route() {
        
        Route::prefix( 'schedule')->name( 'schedule.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            Route::get(   '/index',      'ScheduleController@index'          )->name('index'       );
            Route::get(   '/monthly',    'ScheduleController@index_monthly'  )->name('monthly'     );
            Route::get(   '/weekly',     'ScheduleController@index_weekly'   )->name('weekly'      );
            Route::get(   '/daily',      'ScheduleController@index_daily'    )->name('daily'       );
            Route::get(   '/json_search','ScheduleController@json_search'    )->name('json_search' );
            
            Route::get(   '/create',     'ScheduleController@create'          )->name('create');
            Route::post(  '/create',     'ScheduleController@store'           )->name('store' );
            
            Route::get(   '/show/{schedule}',     'ScheduleController@show'   )->name('show'  )->where( 'schedule', '\d+' );
            Route::get(   '/show',                'ScheduleController@show_m' )->name('show_m');
            
            Route::get(   '/edit/{schedule}',     'ScheduleController@edit'   )->name('edit'   )->where( 'schedule', '\d+' );
            Route::post(  '/edit/{schedule}',     'ScheduleController@update' )->name('update' )->where( 'schedule', '\d+' );
    
            Route::get(     '/delete/{schedule}',     'ScheduleController@delete'  )->name('delete' )->where( 'schedule', '\d+' );
            Route::delete(  '/delete/{schedule}',     'ScheduleController@deleted' )->name('deleted')->where( 'schedule', '\d+' );
    
    
            config(['groupware.schedule.index'    => '予定一覧',
            
                    'groupware.schedule.monthly'  => '月次表示',
                    'groupware.schedule.weekly'   => '週次表示',
                    'groupware.schedule.daily'    => '日次表示',
                    'groupware.schedule.create'   => '新規　予定登録',
                    'groupware.schedule.store'    => '新規　予定登録完了',
                    'groupware.schedule.show'     => '予定内容',
                    'groupware.schedule.detail'   => '予定詳細',
                    'groupware.schedule.edit'     => '予定　変更',
                    'groupware.schedule.update'   => '予定　変更完了',
                    'groupware.schedule.delete'   => '予定　削除',
                    'groupware.schedule.deleted'  => '予定　削除完了',
                    'groupware.schedule.select'   => '予定　選択',
                    ]);
        });
    }
    
    //  ScheduleType ルート
    static public function schedule_type_route() {
        Route::prefix( 'schedule.type')->name( 'schedule.type.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
        
            Route::get(   '/index',                 'ScheduleTypeController@index'      )->name('index'       );
            Route::get(   '/create',                'ScheduleTypeController@create'     )->name('create');
            Route::post(  '/create',                'ScheduleTypeController@store'      )->name('store' );
            Route::get(   '/edit/{schedule_type}',   'ScheduleTypeController@edit'       )->name('edit'   )->where( 'schedule_type', '\d+' );
            Route::post(  '/edit/{schedule_type}',   'ScheduleTypeController@update'     )->name('update' )->where( 'schedule_type', '\d+' );

            config(['groupware.schedule.type.index'    => 'スケジュール種別',
                    'groupware.schedule.type.create'    => '新規スケジュール種別',
                    'groupware.schedule.type.edit'      => 'スケジュール種別 変更',
            
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
    
    //  File ルート
    //
    static public function file_route() {
        Route::prefix( 'file' )->name( 'file.')->namespace( '\App\myHttp\GroupWare\Controllers' )->group( function() {
            
            
            Route::get(   '/index',             'FileController@index'          )->name('index'   );
            Route::get(   '/show/{file}',       'FileController@show'           )->name('show'    )->where( 'file', '\d+' );
            Route::get(   '/detail/{file}',     'FileController@detail'         )->name('detail'  )->where( 'file', '\d+' );
            Route::get(   '/download/{file}',   'FileController@download'       )->name('download')->where( 'file', '\d+' );

            Route::get(   '/select',            'FileController@select'    )->name('select'  );
            Route::get(   '/delete',            'FileController@delete'    )->name('delete'  );
            Route::delete('/delete',            'FileController@deleted'   )->name('deleted' );

            Route::get(   '/json/file_search',   'FileController@json_search'  )->name('json_search');


            config(['groupware.file.index'    => 'ファイル一覧',
                    'groupware.file.show'     => 'ファイル内容',
                    'groupware.file.detail'   => 'ファイル詳細',
                    'groupware.file.select'   => 'ファイル選択削除',
                    'groupware.file.delete'   => 'ファイル削除（確認）',
                    'groupware.file.deleted'  => 'ファイル　削除完了',
                    ]);
        });
        
        
    }
    
            
            
}