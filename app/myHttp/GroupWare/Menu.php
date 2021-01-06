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
        // dump( 'user_menus');
        if( ! Auth( 'user' )) { return "no user_menu"; }
        $wrap = function( $exp ) { return $exp; };
        $options = [ 'from_menu' => '1' ];
        $schedules_options = [ 'from_menu' => 1, 'search_mode' => 2 ];
        
        $route = [  0  => route( 'groupware.user.home', $options  ),
                    1  => route( 'customer.index', $options ),
                    2  => route( 'groupware.schedule.monthly', $schedules_options ),
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
                    
                    
                    ];

        $return = <<<EOT
            <a class="nav-item nav-link" href="$route[1]">顧客管理</a> 
            <a class="nav-item nav-link" href="$route[2]">スケジュール</a>
            <a class="nav-item nav-link" href="$route[14]">Calendar</a> 

            <!--
            <a class="nav-item nav-link" href="$route[15]">Cal prop</a> 
            <a class="nav-item nav-link" href="$route[3]">日報</a>
            <a class="nav-item nav-link" href="$route[5]">ファイル</a>
            -->

            <a class="nav-item nav-link" href="$route[10]">ACL</a>
            <a class="nav-item nav-link" href="$route[11]">Group</a>

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
	                    aria-expanded="false">その他</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1" style="z-index:9999;">
                        <a class="dropdown-item" href="$route[3]">日報</a>
                        <a class="dropdown-item" href="$route[5]">ファイル</a>
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


