<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
// use App\Http\Requests\Vacation\VacationRequest;

class VacationMenu 
{
    //  ユーザメニュー
    //
    public static function user_menus() {
        // dump( 'user_menus');
        if( ! Auth( 'user' )) { return "no user_menu"; }
        $wrap = function( $exp ) { return $exp; };
        $route = [  0  => route( 'vacation.user.detail_mySelf' ),
                    1  => route( 'vacation.application.index' ),
                    2  => route( 'vacation.approval.select' ),
                    10 => route( 'vacation.user.index'),
                    // 11 => route( 'vacation.dept.index'),
                    // 12 => route( 'vacation.approvalMaster.index'),
                    // 13 => route( 'vacation.vacation.index' ),
                    14 => route( 'vacation.common.vindex')
                    
                    ];

        $return = <<<EOT
            <a class="nav-item nav-link" href="$route[0]">ユーザ詳細</a> 
            <a class="nav-item nav-link" href="$route[1]">申請</a> 
            <a class="nav-item nav-link" href="$route[2]">承認</a> 
            <a class="nav-item nav-link" href="$route[14]">休暇検索</a>
            <!--
            <div class="dropdown">
                <a id="dropdownMenuButton"
                        class="nav-item nav-link dropdown-toggle"
	                    data-toggle="dropdown"
                        aria-haspopup="true"
	                    aria-expanded="false">【管理業務】</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                        <a class="dropdown-item" href="">休暇検索</a>
                        <a class="dropdown-item" href="">有給割当</a>
                        <a class="dropdown-item" href="">社員管理</a>
                        <a class="dropdown-item" href="">部署管理</a>
	                    <a class="dropdown-item" href="">申請マスター管理</a>
                </div>
            </div>
            -->
EOT;
        return new HtmlString( $return );
    }

    // 管理者メニュー
    //
    public static function admin_menus() {
    
        if( ! Auth( 'admin' )) { return "no admin_menu"; }
        $wrap = function( $exp ) { return $exp; };
        $route = [  0  => route( 'admin.index' ),
                    1  => route( 'vacation.application.index' ),
                    2  => route( 'vacation.approval.select' ),
                    10 => route( 'vacation.user.index'),
                    // 11 => route( 'vacation.dept.index'),
                    11 => route( 'dept.index'),
                    12 => route( 'vacation.approvalMaster.index'),
                    13 => route( 'vacation.vacation.index' ),
                    14 => route( 'vacation.common.vindex'),
                    15 => route( 'vacation.application.checkProcessed' ),
                    
                    ];

        $return = <<<EOT
    
            <a class="nav-item nav-link" href="$route[14]">休暇検索</a>
            <a class="nav-item nav-link" href="$route[13]">有給割当</a>
            <a class="nav-item nav-link" href="$route[15]">未完確認</a>

            <div class="dropdown">
                <a id="dropdownMenuButton"
                        class="nav-item nav-link dropdown-toggle"
	                    data-toggle="dropdown"
                        aria-haspopup="true"
	                    aria-expanded="false">【管理業務】</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                        <a class="dropdown-item" href="$route[10]">社員管理</a>
                        <a class="dropdown-item" href="$route[11]">部署管理</a>
	                    <a class="dropdown-item" href="$route[0]">管理者管理</a>
	                    <a class="dropdown-item" href="$route[12]">申請マスター管理</a>
                </div>
            </div>
            

EOT;
        return new HtmlString( $return );
    }
}


