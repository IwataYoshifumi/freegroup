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

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;

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

//　今日ボタン用　変数
//
$today = Carbon::today();
$argv_today = Schedule2IndexController::get_argv_for_forms( $request, $today->format('Y-m-d') );
$route_to_today = route( $route_name, $argv_today );

//　表示月切替ボタン用変数の設定
//
$next_month = new Carbon( $base_date->format( 'Y-m-15' ));
$pre_month  = clone $next_month;
$next_month->addMonth();
$pre_month->subMonth();

//　一升あたりの予定表示件数
//
$row_num = 30;

if_debug( $request->all() );


@endphp

@extends('layouts.app')
@section('content')

<div class="cal_table_style">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule2.menu_button' )

            <div class="card cal_table_style">
                <div class="card-header cal_table_style">{{ config( $route_name ) }}</div>

                <div class="card-body cal_table_style">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <div class="w-90 m-2 d-flex">
                        <div class="align-self-start">
                            <!-- 検索フォームボタン・検索ダイヤログ -->
                            @include( 'groupware.schedule2.include_search_form_dialog' )
                        
                            <!-- カレンダー表示切替ボタン・ダイヤログ -->
                            @include( 'groupware.schedule2.include_switch_calendar_display' )
                            
                            <!-- 社員表示切替ボタン・ダイヤログ -->
                            @include( 'groupware.schedule2.include_switch_user_display' )
                    
                            <!-- 今月ボタン -->
                            @if( Arr::first( $dates )->gt( $today ) or Arr::last( $dates )->lt( $today )) 
                                <a class="btn btn-sm btn-outline-dark m-1" href="{{ $route_to_today }}">今月</a>
                            @endif        
                            
                        </div>

                        <!-- カレンダー月表示　月切替ボタン -->
                        <div class="d-flex justify-content-center">
                            <div class="d-flex justify-content-center">
                                <span class="d-flex btn btn_icon switch_month_button" data-base_date='{{ $pre_month->format( 'Y-m-d'  ) }}'>@icon( caret-left )</span>
                                <span class="d-flex btn_icon">{{ $base_date->format( 'Y年 m月' ) }}</span>
                                <span class="d-flex btn btn_icon switch_month_button" data-base_date='{{ $next_month->format( 'Y-m-d' ) }}'>@icon( caret-right )</span>
                            </div>
                        </div>



                        <script>
                            $('.switch_month_button').on( 'click', function() {
                                var month = $(this).data( 'base_date' );
                                $('#base_date').val( month );
                                $('#search_form').submit();
                            });
                        </script>
                    </div>

                    <!-- スケジュール表示 -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
                                    <th>{{ $dayOfWeek }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="">
                            @foreach ($dates as $date)
                                @if ($date->dayOfWeek == 0)
                                    <tr>
                                @endif
                                @php
                                    // if_debug( $date->month );
                                    $date_class = "date_cell cal_cell_style text-truncate schedule";
                                    $d      = $date->format( 'Y-m-d' );
                                    $p_day  = $date->format( 'd' );
                                    
                                    if( $date->eq( $today )) {
                                        $date_class  .= " style_today";
                                    } else {
                                        if($date->month != $base_date->month ) { $date_class .= " bg-light"; }
                                    }
                                @endphp
                                {{-- 日時のボックスを表示 --}}
                                <td class="{{ $date_class }}" data-date="{{ $d }}" >
                                    {{-- 日付を表示 --}}
                                    <div class="font-weight-bold date_button w-100" data-date="{{ $d }}">{{ $p_day }}</div>
                                        @php
                                            $s_ids = [];
                                            $d = $date->format( 'Y-m-d' );
                                            $s_ids = ( array_key_exists( $d, $schedule_ids )) ? $schedule_ids[$d] : [];
                                            $count = count( $s_ids );
                                        @endphp
                                        
                                        {{-- 各日付の予定を下記で表示 --}}
                                        @foreach( $s_ids as $i => $id ) 
                                            @php
                                                $s          = $schedules->find( $id );
                                                $calprop    = $Calprops[$s->calendar_id];
                                                $schedule_class = "schedule schedule_item calendar_" . $s->calendar_id;
                                                $data_schedule = " data-schedule_id='$s->id' data-calendar_id='$s->calendar_id' ";
                                            @endphp 
                                            @if( $i <= $row_num - 1 ) 
                                                {{-- 予定作成者を表示 --}}
                                                <div class="">
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
                                                </div>
                                            @else
                                                他 {{ $count - $row_num }} 件
                                                @break
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                @if ($date->dayOfWeek == 6)
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- スケジュール詳細ダイアログ -->
@include( 'groupware.schedule2.include_show_modal' )

<!-- 日付をクリックで日次表示へ -->
@include( 'groupware.schedule2.include_show_daily' )


@endsection
