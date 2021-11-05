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

$base_date = new Carbon( $request->base_date );
$today = new Carbon( 'today' );

$num_of_weeks = count( $returns['dates'] ) / 7; 

@endphp

@extends('layouts.app')
@section('content')
<div class="main_body">
    
    @include( 'groupware.reservation.monthly_left_and_top_bar' )

    <div class="head_area bg-light" id="head_area">
        <div class="row no-gutters">
            <div class="col border border-dark heddings col1">日</div>
            <div class="col border border-dark heddings col2">月</div>
            <div class="col border border-dark heddings col3">火</div>
            <div class="col border border-dark heddings col4">水</div>
            <div class="col border border-dark heddings col5">木</div>
            <div class="col border border-dark heddings col6">金</div>
            <div class="col border border-dark heddings col7">土</div>
        </div>
    </div>

    <div class="main_area" id="main_area">

        @include( 'groupware.reservation.monthly_body' )
        @include( 'groupware.reservation.monthly_body_reservation' )

    </div>
</div>

<!-- 詳細表示モーダルウィンドウ -->
@include( 'groupware.show_all.dialog.show_detail' )

<!-- 設備予約モーダルウインドウ -->
@include( 'groupware.reservation.modal_to_create_reservation' )



@stack( 'left_and_top_bar_script' )
@stack( 'script_to_move_daily_page' )

@endsection
