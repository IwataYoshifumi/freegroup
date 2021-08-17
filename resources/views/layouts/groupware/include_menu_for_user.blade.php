@php
if( ! Auth( 'user' )) { return "no user_menu"; }

$options = [ 'from_menu' => '1' ];
$schedules_options = [ 'from_menu' => 1, 'search_mode' => 2 ];


$route = [  0  => route( 'groupware.user.home', $options  ),
            1  => route( 'customer.index', $options ),
            2  => route( 'groupware.schedule.monthly', $schedules_options ),
            3  => route( 'groupware.report.index', $options ),
            4  => route( 'groupware.task.index', $options ),
            5  => route( 'groupware.file.index', $options ),
            6  => 'workflow.index',        
            7  => 'facility.index',
            10 => route( 'groupware.access_list.index' ),
            11 => route( 'groupware.group.index' ),
            12 => route( 'groupware.user.index'),
            13 => route( 'dept.index'),
            14 => route( 'groupware.calendar.index'),
            15 => route( 'groupware.tasklist.index'),
            16 => route( 'groupware.report_list.index'),

            20 => route( 'groupware.show_all.monthly' ),
            21 => route( 'groupware.show_all.index', [ 'writable_calender'    => 1, 'set_defaults' => 1 ] ),
            22 => route( 'groupware.show_all.index', [ 'writable_tasklist'    => 1, 'set_defaults' => 1 ] ),
            23 => route( 'groupware.show_all.index', [ 'writable_report_list' => 1, 'set_defaults' => 1 ] ),
            25 => route( 'groupware.show_all.weekly' ),
            
            30 => route( 'groupware.facility.index'      ) . "?from_menu=1" , 
            31 => route( 'groupware.reservation.check'   ) . "?from_menu=1" ,
            32 => route( 'groupware.reservation.index'   ) . "?from_menu=1" ,
            33 => route( 'groupware.reservation.monthly' ) . "?from_menu=1" ,

        ];
@endphp
                    
<a class="nav-item nav-link" href="{{ $route[1]  }}">顧客管理</a> 
<a class="nav-item nav-link" href="{{ $route[20] }}">予定・タスク</a>
<a class="nav-item nav-link" href="{{ $route[21] }}">検索</a>
<a class="nav-item nav-link" href="{{ $route[23] }}">日報</a>

<div class="dropdown">
    <a id="dropdownfacility"
            class="nav-item nav-link dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">設備予約</a>
    <div class="dropdown-menu" aria-labelledby="dropdownfacility" style="z-index:9999;">
        <a class="dropdown-item" href="{{ $route[33] }}">予約状況</a>
        <a class="dropdown-item" href="{{ $route[32] }}">予約一覧</a>
        <div class="dropdown-divider"></div>        
        <a class="dropdown-item" href="{{ $route[30] }}">設備管理</a>
    </div>
</div>


@if( 0 )
    <a class="nav-item nav-link" href="{{ $route[4]  }}">【旧】タクス</a>
    <a class="nav-item nav-link" href="{{ $route[3]  }}">【旧】日報</a>
@endif

<div class="dropdown">
    <a id="dropdownMenuButton"
            class="nav-item nav-link dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">設定</a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
        <a class="dropdown-item" href="{{ $route[10] }}">アクセスリスト設定</a>
        <a class="dropdown-item" href="{{ $route[11] }}">グループ設定</a>
        <a class="dropdown-item" href="{{ $route[5]  }}">ファイル管理</a>
        <div class="dropdown-divider"></div>        
        <a class="dropdown-item" href="{{ $route[30]  }}">設備管理</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ $route[14]  }}">カレンダー設定</a>
        <a class="dropdown-item" href="{{ $route[15]  }}">タスクリスト設定</a>
        <a class="dropdown-item" href="{{ $route[16]  }}">日報リスト設定</a>
        <div class="dropdown-divider"></div>        
        <a class="dropdown-item" href="{{ $route[12] }}">社員一覧</a>
        <a class="dropdown-item" href="{{ $route[13] }}">部署一覧</a>
    </div>
</div>