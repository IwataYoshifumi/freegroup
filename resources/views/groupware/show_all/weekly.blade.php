@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Controllers\Search\SearchSchedule;
use App\myHttp\GroupWare\Controllers\Search\SearchSchedulesAndTasks;
use App\myHttp\GroupWare\Controllers\Schedule2IndexController;

$route_name = Route::currentRouteName();

$user_id = user_id();

$sidebar_height = 30;

$calendars = [ 0 => "岩田のカレンダー", 1 => "総務部のカレンダー", 2 => "営業部のカレンダー", 3 => "鈴木のカレンダー", 4 => "全社カレンダー" ];
$tasklists = [ 0 => "岩田のタスク", 1 => "総務部のタスク", 2 => "営業部のタスク", 3 => "鈴木のタスク" ];

$calprops  = Calprop::whereCalendarsCanRead( user_id() )->get();
$taskprops = TaskProp::whereTaskListCanRead( user_id() )->get();

$base_date = new Carbon( $request->base_date );

$today = new Carbon( 'today' );
$num_of_weeks = count( $returns['dates'] ) / 7; 

if_debug( $request->all() );

@endphp

@extends('layouts.app')
@section('content')
<div class="main_body">
    {{--
    include( 'groupware.show_all.monthly_left_and_top_bar' )
    --}}
    
    @include( 'groupware.show_all.menu_bar_left' )
    @include( 'groupware.show_all.menu_bar_top' )

    <div class="head_area bg-light" id="head_area">
        <div class="row no-gutters">
            <div class="col border border-dark heddings col01 span01">部署・社員名</div>
            <div class="col border border-dark heddings col02 span01">日</div>
            <div class="col border border-dark heddings col03 span01">月</div>
            <div class="col border border-dark heddings col04 span01">火</div>
            <div class="col border border-dark heddings col05 span01">水</div>
            <div class="col border border-dark heddings col06 span01">木</div>
            <div class="col border border-dark heddings col07 span01">金</div>
            <div class="col border border-dark heddings col08 span01">土</div>
        </div>
    </div>

    <div class="main_area" id="main_area">

        @include( 'groupware.show_all.weekly_body' )
        @include( 'groupware.show_all.weekly_body_items' )

    </div>
</div>

@include( 'groupware.show_all.weekly_scripts' )

<!-- スケジュール詳細ダイアログ -->
<!--include( 'groupware.show_all.modal_to_show_detail' )-->
@include( 'groupware.show_all.dialog.show_detail' )

@stack( 'left_and_top_bar_script' )
@stack( 'script_to_move_daily_page' )

@endsection
