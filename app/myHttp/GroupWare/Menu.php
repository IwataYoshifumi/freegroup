<?php

namespace App\myHttp\GroupWare;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use DB;

use App\Http\Controllers\Controller;

class Menu 
{
    //  ユーザメニュー
    //
    public static function user_menus() {
        // if_debug( 'user_menus');
        if( ! Auth( 'user' )) { return "no user_menu"; }
        $wrap = function( $exp ) { return $exp; };
        $options = [ 'from_menu' => '1' ];
        $schedules_options = [ 'from_menu' => 1, 'search_mode' => 2 ];
        
        $route = [  0  => route( 'groupware.user.home', $options  ),
                    1  => route( 'customer.index', $options ),
                    2  => route( 'groupware.schedule.monthly', $schedules_options ),
                    // 2  => route( 'groupware.schedule.index', $schedules_options ),
                    3  => route( 'groupware.report.index', $options ),
                    4  => 'todo.index',
                    5  => route( 'groupware.file.index', $options ),
                    6  => 'workflow.index',        
                    7  => 'facility.index',
                    10 => route( 'groupware.access_list.index' ),
                    11 => route( 'groupware.group.index' ),
                    12 => route( 'groupware.user.index'),
                    13 => route( 'dept.index'),
                    14 => route( 'groupware.calendar.index'),
                    15 => route( 'groupware.calprop.index'),
                    16 => route( 'groupware.report_list.index'),
                

                    //
                    //　開発用ルート
                    //
                    99 => route( 'groupware.test.files'),
                    98 => route( 'groupware.file.deleteAllUntachedFiles'),

                    // スクリーンサイズ系
                    97 => route( 'screensize.get' ),
                    96 => route( 'screensize.dump' ),
                    95 => route( 'screensize.forget' ),
                    
                    // 複数ファイル削除
                    94 => route( 'groupware.test.delete_files'),
                    
                    // テスト
                1000   => route( 'groupware.test.template'),
                1001   => route( 'groupware.test.test'),
                1002   => route( 'groupware.test.custome_blade_icons'),
                1003   => route( 'groupware.test.depts_users_customers'),

                    ];


        $return = <<<EOT
            <a class="nav-item nav-link" href="$route[1]">顧客管理</a> 
            <a class="nav-item nav-link" href="$route[2]">スケジュール</a>
            
            <a class="nav-item nav-link" href="$route[3]">日報</a>

            <!--
            <a class="nav-item nav-link" href="$route[0]">【ホーム】</a> 
            <a class="nav-item nav-link" href="$route[4]">【ToDo】</a>
            <a class="nav-item nav-link" href="$route[5]">【稟議/決裁】</a>
            <a class="nav-item nav-link" href="$route[5]">【施設備品/予約】</a>
            -->
            
            <div class="dropdown">
                <a id="dropdownMenuButton1"
                        class="nav-item nav-link dropdown-toggle"
	                    data-toggle="dropdown"
                        aria-haspopup="true"
	                    aria-expanded="false">開発用</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="z-index:9999;">


                        <a role="button" class="dropdown-toggle dropdown-item" id="Test" data-toggle="dropdown">Test</a>
                            <div class="dropdown-menu" aria-labelledby='Test'>

                                <a class="dropdown-item" href="$route[1000]">Test Template</a>
                                <a class="dropdown-item" href="$route[1001]">Test</a>
                                <a class="dropdown-item" href="$route[1002]">Blade Icons</a>
                                <a class="dropdown-item" href="$route[1003]">Depts Users Customers</a>
                            </div>


                        <a role="button" class="dropdown-toggle dropdown-item" id="File" data-toggle="dropdown">File</a>
                            <div class="dropdown-menu" aria-labelledby='File'>
        

                                <a class="dropdown-item" href="$route[94]">複数ファイル削除</a>
                                <a class="dropdown-item" href="$route[98]">無添付全ファイル一括削除</a>
                                <a class="dropdown-item" href="$route[99]">ファイル入力コンポーネント</a>
                            </div>
                        
                        
                        <a role="button" class="dropdown-toggle dropdown-item" id="ScreenSize" data-toggle="dropdown">ScreenSize</a>
                            <div class="dropdown-menu" aria-labelledby='ScreenSize'>
                                <a class="nav-item nav-link" href="$route[97]">set</a> 
                                <a class="nav-item nav-link" href="$route[96]">dump</a> 
                                <a class="nav-item nav-link" href="$route[95]">forget</a> 
                            </div>
                </div>
            </div>
            
            <div class="dropdown">
                <a id="dropdownMenuButton"
                        class="nav-item nav-link dropdown-toggle"
	                    data-toggle="dropdown"
                        aria-haspopup="true"
	                    aria-expanded="false">設定</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                        <a class="dropdown-item" href="$route[12]">社員一覧</a>
                        <a class="dropdown-item" href="$route[13]">部署一覧</a>
                        <a class="dropdown-item" href="$route[10]">アクセスリスト設定</a>
                        <a class="dropdown-item" href="$route[10]">グループ設定</a>
                        <a class="dropdown-item" href="$route[5]">ファイル管理</a>
                </div>
            </div>

EOT;
        return new HtmlString( $return );
    }

    // 管理者メニュー
    //
    public static function admin_menus() {
    
        if( ! Auth( 'admin' )) { return "no admin_menu"; }
        $wrap = function( $exp ) { return $exp; };
        $route = [  0  => route( 'admin.index' ),
                    1  => route( 'groupware.user.index' ),
                    3  => route( 'dept.index'),
                    4  => route( 'groupware.role_group.index' ),
                    ];

        $return = <<<EOT
            <a class="nav-item nav-link" href="$route[1]">社員管理</a>
            <a class="nav-item nav-link" href="$route[3]">部署管理</a>
            <a class="nav-item nav-link" href="$route[4]">ロール設定</a>
            <a class="nav-item nav-link" href="$route[0]">管理者管理</a>


            <!--
            <a class="nav-item nav-link" href="">休暇検索</a>
            <a class="nav-item nav-link" href="">有給割当</a>
            <a class="nav-item nav-link" href="">未完確認</a>

            <div class="dropdown">
                <a id="dropdownMenuButton"
                        class="nav-item nav-link dropdown-toggle"
	                    data-toggle="dropdown"
                        aria-haspopup="true"
	                    aria-expanded="false">【管理業務】</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                        <a class="dropdown-item" href="$route[1]">社員管理</a>
                        <a class="dropdown-item" href="$route[3]">部署管理</a>
	                    <a class="dropdown-item" href="$route[0]">管理者管理</a>

                </div>
            </div>
            -->

EOT;
        return new HtmlString( $return );
    }
}


