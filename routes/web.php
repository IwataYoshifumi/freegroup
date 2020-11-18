<?php

use Illuminate\Support\Facades\Route;
use app\Http\Helpers\BackButton;

use App\Http\Controllers\Vacation\VacationRouter;
use App\myHttp\GroupWare\Router as GroupWareRouter;

// use App\Http\Controllers\Vacation\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return view('welcome'); })->name( 'welcome' );
Route::get('/home', 'HomeController@index')->name('home');

// User　認証系
Route::namespace('User')->prefix('user')->name('user.')->group(function () {

    Auth::routes([
        'register' => false,
        'reset'    => true,
        'verify'   => false
    ]);
    Route::middleware('auth:user')->group(function () {
        Route::get( '/',           'HomeController@index');
        Route::get( '/home',       'HomeController@index')->name( 'home' );
        Route::get( '/change_password',  'UserController@password'      )->name( 'change_password' );
        Route::post('/change_password',  'UserController@updatePassword')->name( 'change_password' );
        
        Route::get( '/json/search', "UserController@json" )->name( 'json.search' );
    });
});

// Userモデル
Route::middleware('auth:admin')->namespace( 'User' )->prefix( 'user' )->name( 'user.' )->group(function () {

        Route::get( '/{user}',     'UserController@show'            )->name('show'  )->where( 'user', '\d+' );
        Route::get( '/index',      'UserController@index'           )->name('index' );
        Route::get( '/create',     'UserController@create'          )->name('create');
        Route::post('/store',      'UserController@store'           )->name('store' );
        Route::get( '/{user}/edit', 'UserController@edit'           )->name('edit'  )->where( 'user', '\d+' );
        Route::post( '/{user}/edit',    'UserController@update'     )->name('update')->where( 'user', '\d+' );
        Route::get( '/select',     'UserController@select'          )->name('select');
        Route::get( '/retire',     'UserController@retire'          )->name('retire');
        Route::get( '/csv',        'UserController@csv'             )->name('csv'   );
        
        config(['user.index'    => '社員一覧',
                'user.create'   => '新規　社員登録',
                'user.store'    => '新規　社員登録完了',
                'user.show'     => '社員情報',
                'user.detail'   => '社員情報（詳細）',
                'user.edit'     => '社員情報　変更',
                'user.home'     => '社員ホーム',
                ]);
});

// Deptモデル（部署モデル）
Route::middleware([ 'auth:admin' ])->group(function () {
    Route::get( '/dept/index',          'DeptController@index'    )->name('dept.index'    );
    Route::get( '/dept/create',         'DeptController@create'   )->name('dept.create'   );
    Route::post('/dept/store',          'DeptController@store'    )->name('dept.store'    );
    Route::get( '/dept/{dept}',         'DeptController@show'     )->name('dept.show'     )->where( 'dept', '[0-9]+' );
    Route::get( '/dept/{dept}/edit',    'DeptController@edit'     )->name('dept.edit'     )->where( 'dept', '[0-9]+' );
    Route::post( '/dept/{dept}',        'DeptController@update'   )->name('dept.update'   )->where( 'dept', '[0-9]+' );
    Route::get( '/dept/{dept}/destroy', 'DeptController@destroy'  )->name('dept.destory'  )->where( 'dept', '[0-9]+' );
    Route::post( '/dept/{dept}/destroy','DeptController@destroyed')->name('dept.destoryed')->where( 'dept', '[0-9]+' );

    config(['dept.index'    => '部署一覧',
            'dept.create'   => '新規　部署登録',
            'dept.store'    => '新規　部署登録完了',
            'dept.show'     => '部署情報',
            'dept.edit'     => '部署情報　変更',
            'dept.update'   => '部署情報　変更完了',
            'dept.destory'  => '部署　削除',
            'dest.destorid' => '部署　削除実行',
        ]);
});

// Admin　認証系
Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    
    Auth::routes([
        'register' => false,
        'reset'    => true,
        'verify'   => false
    ]);
    Route::middleware('auth:admin')->group(function () {
        Route::get( '/',           'HomeController@index' );
        Route::get( '/home',       'HomeController@index'                )->name( 'home' );
        Route::get( '/change_password',  'AdminController@password'      )->name( 'change_password' );
        Route::post('/change_password',  'AdminController@updatePassword')->name( 'change_password' );
    });
});

//  Adminモデル
Route::middleware('auth:admin')->namespace( 'Admin' )->prefix( 'admin' )->name( 'admin.' )->group(function () {

        Route::get( '/{admin}',    'AdminController@show'            )->name('show'  )->where( 'admin', '\d+' );
        Route::get( '/index',      'AdminController@index'           )->name('index' );
        Route::get( '/create',     'AdminController@create'          )->name('create');
        Route::post('/store',      'AdminController@store'           )->name('store' );
        Route::get( '/{admin}/edit', 'AdminController@edit'           )->name('edit'  )->where( 'admin', '\d+' );
        Route::post( '/{admin}/edit',    'AdminController@update'     )->name('update')->where( 'admin', '\d+' );
        // Route::get( '/select',     'AdminController@select'          )->name('select');
        // Route::post('/retire',     'AdminController@retire'          )->name('retire');
        Route::get( '/csv',        'AdminController@csv'             )->name('csv'   );
        
        config(['admin.index'    => '管理者一覧',
                'admin.create'   => '新規　管理者登録',
                'admin.store'    => '新規　管理者登録完了',
                'admin.show'     => '管理者情報',
                'admin.detail'   => '管理者情報（詳細）',
                'admin.edit'     => '管理者情報　変更',
                'admin.select'   => '管理者　選択',
                
                ]);
});

// Customerモデル
//
Route::middleware('auth:user')->namespace( 'Customer' )->prefix( 'customer' )->name( 'customer.' )->group(function () {

        Route::get( '/{customer}',      'CustomerController@show'            )->name('show'  )->where( 'customer', '\d+' );
        Route::get( '/index',      'CustomerController@index'           )->name('index' );
        Route::get( '/create',     'CustomerController@create'          )->name('create');
        Route::post('/create',     'CustomerController@store'           )->name('store' );
        Route::get( '/{customer}/edit', 'CustomerController@edit'           )->name('edit'  )->where( 'customer', '\d+' );
        Route::post( '/{customer}/edit',    'CustomerController@update'     )->name('update')->where( 'customer', '\d+' );
        
        Route::get( '/{customer}/delete',  'CustomerController@delete'     )->name('delete')->where( 'customer', '\d+' );
        Route::delete( '/{customer}/delete',  'CustomerController@deleted'   )->name('deleted')->where( 'customer', '\d+' );
        
        Route::get( '/csv',        'CustomerController@csv'             )->name('csv'   );
        
        //  JSON
        //
        Route::get( '/json/search', "CustomerController@json" )->name( 'json.search' );
        
        config(['customer.index'    => '顧客一覧',
                'customer.create'   => '新規　顧客登録',
                'customer.store'    => '新規　顧客登録完了',
                'customer.show'     => '顧客情報',
                'customer.detail'   => '顧客情報（詳細）',
                'customer.edit'     => '顧客情報　変更',
                'customer.delete'     => '顧客　削除',
                'customer.deleted'     => '顧客　削除完了',
                'customer.select'   => '顧客　選択',
                ]);
});

// グループウエアシステム
//
GroupWareRouter::route();

// テストルート
//
// Route::prefix('/')->group( function() {

// });

//　戻るボタンのルート
//
BackButton::route();