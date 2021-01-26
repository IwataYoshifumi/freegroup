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



#dump( Request::all() );
#dump( session( 'back_button' ) );


$users     = ( isset( $request->users )) ? $request->users : [];
$route_name = Route::currentRouteName();

//　関連社員を表示
//
$show_attendees = ( $request->display_axis == 'users' ) ? true : false ;

//　検索データ（各クラスインスタンスのコレクション）
//
$Calprops  = op( $returns )['calprops'];
$Calendars = op( $returns )['calendars'];
$Users = op( $returns )['users'];
$Depts = op( $returns )['depts'];


//　縦軸をカレンダーするための表示用データ
//
$calendars_schedules = [];
foreach( $schedules as $s ) {
    $calendars_schedules[$s->calendar_id] = [];
}
foreach( $schedules as $s ) {
    array_push( $calendars_schedules[$s->calendar_id] , $s->id );
}


# if_debug( $calendars_schedules, $Calprops );

//　今日ボタン用　変数
//
$next_month = new Carbon( $base_date->format( 'Y-m-15' ));
$pre_month  = clone $next_month;
$next_month->addMonth();
$pre_month->subMonth();

$max_rows = 7;

$today = Carbon::today(); 

$argv_1 = Schedule2IndexController::get_argv_for_forms( $request, $base_date->copy()->subDays(7)->format('Y-m-d') );
$argv_2 = Schedule2IndexController::get_argv_for_forms( $request, $base_date->copy()->addDays(7)->format('Y-m-d') );
if( Arr::first( $dates )->month == Arr::last( $dates )->month ) {
    $print_period =  Arr::first( $dates )->format( 'Y年n月j' )."～".Arr::last( $dates )->day."日"; 
} else {
    $print_period = Arr::first( $dates )->format( 'Y年n月j日' )."～".Arr::last( $dates )->format( 'n月j日' );
}
$argv_today = Schedule2IndexController::get_argv_for_forms( $request, $today->format( 'Y-m-d' ) );
$route_to_today = route( $route_name , $argv_today );

@endphp

@extends('layouts.app')


@section('content')

<div class="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )



                    <div class="row m-2 w-100 w-md-50">
                        
                        <a class="btn btn_icon" href="{{ route( Route::currentRouteName(), $argv_1 ) }}">@icon( caret-left )</a>
                        <div class="text-nowrap h4 bg-light">{{ $print_period }}</div>
                        <a class="btn btn_icon" href="{{ route( Route::currentRouteName(), $argv_2 ) }}">@icon( caret-right )</a>
                        <div class="col-1"></div>
                        
                        @if( Arr::first( $dates )->gt( $today ) or Arr::last( $dates )->lt( $today )) 
                            <a class="btn btn-sm btn-outline-dark m-1" href="{{ $route_to_today }}">今日</a>
                        @endif
                        
                        <!-- 検索フォームボタン・検索ダイヤログ -->
                        @include( 'groupware.schedule2.include_search_form_dialog' )
                        
                        <!-- カレンダー表示切替ボタン・ダイヤログ -->
                        @include( 'groupware.schedule2.include_switch_calendar_display' )
                        
                        <!-- 社員表示切替ボタン・ダイヤログ -->
                        @include( 'groupware.schedule2.include_switch_user_display' )

                    </div>

                    <div class="d-none d-lg-block">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @php
                                        $days = [ '日', '月', '火', '水', '木', '金', '土'];
                                        
                                    @endphp
                                    
                                    @foreach ( $dates as $date )
                                        @if( $loop->first )
                                            <th style='text-align:center width:14.2%' class="bg-light">
                                                <div class=" show_daily schedule" >カレンダー名</div>
                                            </th>
                                        @endif
                                        <th style='text-align:center width:14.2%' class="bg-light">
                                            <div class=" show_daily schedule" data-date="{{ $date }}">
                                                {{ $date->format( 'n/j' ) }}【{{ $days[ $date->format( 'w' ) ] }}】
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $calendars_schedules as $calendar_id => $calendar_schedule_ids )
                                    @php
                                        $calprop  = $Calprops->get( $calendar_id );
                                        $calendar = $Calendars->find( $calendar_id );
                                        $title    = "公開種別：". op( Calendar::getTypes() )[ $calendar->type ];
                                        $class_name = "calendar_" . $calendar_id;
                                        
                                    @endphp
                                    <tr class="{{ $class_name }}">
                                        <th>
                                            <div class="uitooltip" style="{{ $calprop->style() }}" title="{{ $title }}">
                                                {{ $calprop->name }}
                                            </div>
                                        </th>
                                        
                                        @foreach ($dates as $date)
                                            @php
                                                $today_style = ( $date->eq( $today )) ? "style_today" : "";
                                                $class="date_cell $today_style";
                                                if($date->month != $base_date->month ) { $class="bg-light"; }
                                                if( $date->eq( $base_date )) { $class.=" border border-primary"; }
                                                $d = $date->format( 'Y-m-d' );
                                                //　日付内にスケジュールがあるか確認
                                                //
                                                $s_ids = [];
                                                if( array_key_exists( $d, $schedule_ids )) {
                                                    $s_ids = $schedule_ids[$d]; 
                                                }
                                                $i = 1;
                                            @endphp
                                            <td class="{{ $class }}" data-date="{{ $d }}">
                                                @foreach( $s_ids as $id )
                                                    @if( ! in_array( $id, $calendar_schedule_ids )) @continue @endif
                                                    @php 
                                                        $s = $schedules->find($id); 
                                                        if( $i >= $max_rows ) {
                                                            printf( '<div class="show_daily schedule" data-date="%s">・・・</div>', $d );
                                                            break;
                                                        }
                                                        $i++;
                                                        $calprop = $Calprops[$s->calendar_id];
                                                        
                                                        $schedule_class = "schedule_item calendar_" . $s->calendar_id;
                                                        $data_schedule = " data-schedule_id='$s->id' data-calendar_id='$s->calendar_id' ";

                                                    @endphp
                                                    {{-- 予定作成者を表示 --}}
                                                    @if( $s->user ) 
                                                        <div style="{{ $calprop->style() }}" class="{{ $schedule_class }} user_{{ $s->user->id }}" {!! $data_schedule !!}>
                                                            <div class="d-flex">
                                                                <div class="mr-auto">{{ $s->user->name }}：{{ $s->name }}</div>
                                                                <div class="ml-auto">{{ $s->start_time() }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    {{-- 予定関連社員の表示 --}}
                                                    @if( $show_attendees && count( $s->users ))
                                                        @foreach( $s->users as $u )
                                                            @if( $s->user_id == $u->id ) @continue @endif
                                                            <div style="{{ $calprop->style() }}" class="{{ $schedule_class }} user_{{ $u->id}}" {!! $data_schedule !!}>
                                                                <div class="d-flex">
                                                                    <div class="mr-auto">{{ $u->name         }}：{{ $s->name }}</div>
                                                                    <div class="ml-auto">＊{{ $s->start_time() }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>                                    
                    </div>

                    

        
                </div>
            </div>
        </div>
    </div>
</div>
<!-- スケジュール詳細ダイアログ -->
@include( 'groupware.schedule2.include_show_modal' )


@endsection

