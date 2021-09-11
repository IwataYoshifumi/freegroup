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

use App\Http\Helpers\ScreenSize;

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
        @include( 'groupware.show_all.mobile.side_bar' )
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
      -- トップバー（月表示、月切替ボタン、予定・タクス新規作成ボタン）
      --    
      -->
    <div class="bg-info mb_top_bar" id="top_bar">
        @include( 'groupware.show_all.mobile.monthly_top_bar' )
    </div>

    <!--
      --    
      -- 曜日表示（ヘッダー領域）
      --    
      -->
    <div class="bg-light mb_head_area" id="head_area">
        @include( 'groupware.show_all.mobile.monthly_top_dates' )
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
        @include( 'groupware.show_all.mobile.monthly_body' )
        <!--
          --    
          -- 予定・タスクの表示
          --    
          -->
        @include( 'groupware.show_all.monthly_body_schedules_tasks' )
    </div>
</div>

<!-- 日付詳細表示ダイアログ -->
@include( 'groupware.show_all.mobile.daily_modal' )


<!-- スケジュール詳細ダイアログ -->
<!--include( 'groupware.show_all.modal_to_show_detail' )-->
@include( 'groupware.show_all.dialog.show_detail' )


<!-- スクリーンサイズを取得 -->
{{ ScreenSize::rendarScriptToGetScreenSize() }}


@endsection
