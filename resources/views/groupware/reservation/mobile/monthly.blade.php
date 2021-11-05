@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\ScreenSize;
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

@extends('layouts.app_mb')
@section('content')

<div class="mb_main_body" id="main_calender">
    <!--
      --    
      -- サイドバー（予定・タスク検索フォーム
      --    
      -->
    <div class="mb_side_bar" id="side_bar">
        <a class="btn" id="side_bar_closer">@icon( angle-double-left ) 閉じる</a>
        @include( 'groupware.reservation.mobile.side_bar' )
    </div>
    <script>
    
        var side_bar = $('#side_bar');
        side_bar.hide();
        
        $("#side_bar_closer").on( 'click', function() { 
            side_bar.toggle( 'slide', { percent: 50, }, 100 );
        });
    </script>
    
    <!--
      --    
      -- トップバー（月表示、月切替ボタン、予約ボタン）
      --    
      -->
    <div class="mb_top_bar border border-dark" id="top_bar" style="background-color: palegreen;">
        @include( 'groupware.reservation.mobile.monthly_top_bar' )
    </div>

    <!--
      --    
      -- 曜日表示（ヘッダー領域）
      --    
      -->
    <div class="bg-light mb_head_area" id="head_area">
        @include( 'groupware.reservation.mobile.monthly_top_dates' )
    </div>

    <!-- 
      --
      -- カレンダー表示領域
      --
      --> 
    <div class="mb_main_area" id="main_area">
        <!--
          --    
          -- 日付のマス表示
          --    
          -->
        @include( 'groupware.reservation.mobile.monthly_body' )
        <!--
          --    
          -- 予約の表示
          --    
          -->
        @include( 'groupware.reservation.monthly_body_reservation' )
    </div>
</div>

<!-- 日次表示ダイアログ -->
@include( 'groupware.reservation.mobile.daily_modal' )


<!-- 詳細ダイアログ -->
@include( 'groupware.show_all.dialog.show_detail' )

<!-- 設備予約モーダルウインドウ -->
@include( 'groupware.reservation.modal_to_create_reservation' )

<!-- スクリーンサイズを取得 -->
{{ ScreenSize::rendarScriptToGetScreenSize() }}




@endsection
