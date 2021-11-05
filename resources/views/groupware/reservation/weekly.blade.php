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

$dates = $returns['dates'];

$today     = new Carbon( 'today' );
$base_date = Arr::first( $dates )->copy();

#if_debug( $request->all(), $returns );

@endphp

@extends('layouts.app')
@section('content')
<div class="main_body">
    
    @include( 'groupware.reservation.weekly_menu_bar_left' )
    @include( 'groupware.reservation.weekly_menu_bar_top' )

    <div class="head_area bg-light" id="head_area">
        <div class="row no-gutters">
            @php
            $i = 2;
            @endphp
            
            @foreach( $dates as $d ) 
                @if( $loop->first )
                <div class="col border border-dark heddings col01">設備名</div>
                @endif
                <div class="col border border-dark heddings col0{{ $i }}">&nbsp;{{ $d->format( 'n/j') }}（{{ p_date_jp( $d->format('w')) }}）</div>

                {{--
                <div class="col border border-dark heddings col02">日</div>
                <div class="col border border-dark heddings col03">月</div>
                <div class="col border border-dark heddings col04">火</div>
                <div class="col border border-dark heddings col05">水</div>
                <div class="col border border-dark heddings col06">木</div>
                <div class="col border border-dark heddings col07">金</div>
                <div class="col border border-dark heddings col08">土</div>
                --}}
                @php $i++ @endphp
            @endforeach
        </div>
    </div>

    <div class="main_area" id="main_area">

        @include( 'groupware.reservation.weekly_body' )
        @include( 'groupware.reservation.weekly_body_items' )

    </div>
</div>


<!-- 詳細表示ダイアログ -->
@include( 'groupware.show_all.dialog.show_detail' )

<!-- 設備予約モーダルウインドウ -->
@include( 'groupware.reservation.modal_to_create_reservation' )

@include( 'groupware.reservation.weekly_scripts' )

@stack( 'left_and_top_bar_script' )

@endsection
