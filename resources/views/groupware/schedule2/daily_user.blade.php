@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;
use App\myHttp\Groupware\Models\Schedule;
use App\myHttp\Groupware\Models\Calendar;
use App\myHttp\Groupware\Models\CalProp;
use App\myHttp\GroupWare\Models\Dept;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;

use App\Http\Helpers\BackButton;

// 初期化
//
$users     = ( isset( $request->users )) ? $request->users : [];
$route_name = Route::currentRouteName();

//　関連社員を表示
//
$show_attendees = ( $request->display_axis == 'users' ) ? true : false ;

//　検索データ（各クラスインスタンスのコレクション）
//
$Calprops       = op( $returns )['calprops'];
$Calendars      = op( $returns )['calendars'];
$Users          = op( $returns )['users'];
$Depts          = op( $returns )['depts'];
$Schedules      = op( $returns )['schedules'];
$schedule_ids   = op( $returns )['schedule_ids'];


//　縦軸を社員名するための表示用データ
//
$users_schedules = [];
$users_in_depts  = [];

foreach( $Users as $u ) { $users_schedules[$u->id] = []; }

foreach( $Schedules as $s ) {
    if( $s->user ) { 
        array_push( $users_schedules[$s->user->id] ,    $s );
    }
    if( ! empty( $s->users ) and count( $s->users )) {
        foreach( $s->users as $u ) {
            if( op( $s->user )->id == $u->id ) { continue; }
            array_push( $users_schedules[$u->id], $s );
        }
    }
}

if_debug( __FILE__, $returns, $Depts, $Users, $users_in_depts );

//　今日ボタン、次の日、前日ボタン用
//
$today = Carbon::today(); 

$argv_pre   = Schedule2IndexController::get_argv_for_forms( $request, $base_date->copy()->subDay()->format('Y-m-d') );
$argv_next  = Schedule2IndexController::get_argv_for_forms( $request, $base_date->copy()->addDay()->format('Y-m-d') );
$argv_today = Schedule2IndexController::get_argv_for_forms( $request, $today->format( 'Y-m-d' ) );

$route_to_pre_date  = route( $route_name, $argv_pre   );
$route_to_next_date = route( $route_name, $argv_next  );
$route_to_today     = route( $route_name, $argv_today );

@endphp

@extends('layouts.app')

@section('content')

<div class="">
    <div class="row justify-content-center">
        <div class="col-8">
            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )



                    <div class="row m-2 w-100 w-md-50">
                        
                        <a class="btn btn_icon" href="{{ $route_to_pre_date  }}">@icon( caret-left )</a>
                        <div class="text-nowrap h4 bg-light">{{ $base_date->format( 'Y-m-d' ) }} 【{{ p_day_of_week( $base_date ) }}】</div>
                        <a class="btn btn_icon" href="{{ $route_to_next_date }}">@icon( caret-right )</a>
                        <div class="col-1"></div>
                        
                        @if( ! $today->eq( $base_date ))  
                            <a class="btn btn-sm btn-outline-dark m-1" href="{{ $route_to_today }}">今日</a>
                        @endif
                        
                        <!-- 検索フォームボタン・検索ダイヤログ -->
                        @include( 'groupware.schedule2.include_search_form_dialog' )
                        
                        <!-- カレンダー表示切替ボタン・ダイヤログ -->
                        @include( 'groupware.schedule2.include_switch_calendar_display' )
                        
                        <!-- 社員表示切替ボタン・ダイヤログ -->
                        @include( 'groupware.schedule2.include_switch_user_display' )

                    </div>

                    @if( count( $Schedules ))
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>部署名</th>
                                    <th>社員名</th>
                                    <th>予定</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $Depts as $dept )
                                    @php $rowspan = count( $dept->users ); @endphp
                                    <tr><td rowspan="{{ $rowspan }}">{{ $dept->name }} {{ $rowspan }}</td>
                                        @foreach( $dept->users as $user )
                                            @php $user_id_class = "user_" . $user->id; @endphp
                                            @if( ! $loop->first ) <tr> @endif
                                            <td class="{{ $user_id_class }}">{{ $user->name }}</td>                                   
                                            <td class="{{ $user_id_class }}">
                                            @foreach( $users_schedules[$user->id] as $schedule )
                                                
                                                @php
                                                $calprop        = $Calprops[$schedule->calendar_id];
                                                $schedule_class = "schedule schedule_item calendar_" . $schedule->calendar_id;
                                                $data_schedule  = " data-schedule_id='$schedule->id' data-calendar_id='$schedule->calendar_id' ";
                                                @endphp
                                                
                                                <div style="{{ $calprop->style() }}" class="{{ $schedule_class }}" {!! $data_schedule !!}>   {{-- htmlspecialchars OK --}}
                                                    <div class="d-flex">
                                                        <div class="mr-auto">{{ $schedule->name }} @if( $schedule->user->id != $user->id ) ＊@endif </div>
                                                        <div class="ml-auto">{{ $s->p_time() }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            </td>
                                            @if( ! $loop->last ) </tr> @endif    
                                        @endforeach

                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div>
                        {{ BackButton::form() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- スケジュール詳細ダイアログ -->
@include( 'groupware.schedule2.include_show_modal' )


@endsection

