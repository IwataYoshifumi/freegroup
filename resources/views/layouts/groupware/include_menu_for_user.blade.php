@php
if( ! Auth( 'user' )) { return "no user_menu"; }

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
            16 => route( 'groupware.report_list.index'),
            100 => route( 'groupware.test.search_report_lists' ),
        ];
@endphp
                    
<a class="nav-item nav-link" href="{{ $route[1] }}">顧客管理</a> 
<a class="nav-item nav-link" href="{{ $route[2] }}">スケジュール</a>
<a class="nav-item nav-link" href="{{ $route[3] }}">日報</a>
@if( is_debug() ) 
    <a class="nav-item nav-link" href="{{ $route[100] }}">RL</a>
@endif

<div class="dropdown">
    <a id="dropdownMenuButton"
            class="nav-item nav-link dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">設定</a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
            <a class="dropdown-item" href="{{ $route[12] }}">社員一覧</a>
            <a class="dropdown-item" href="{{ $route[13] }}">部署一覧</a>
            <a class="dropdown-item" href="{{ $route[10] }}">アクセスリスト設定</a>
            <a class="dropdown-item" href="{{ $route[11] }}">グループ設定</a>
            <a class="dropdown-item" href="{{ $route[5]  }}">ファイル管理</a>
    </div>
</div>