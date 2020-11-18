@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ScheduleType;
use App\myHttp\GroupWare\Controllers\ScheduleController;

use App\Models\Vacation\Dept;

#dump( Request::all() );
#dump( session( 'back_button' ) );

$users     = ( isset( $request->users )) ? $request->users : [];
$relation_schedule_type = ScheduleType::get_schedule_type_of_relation_class( auth( 'user' )->id() );

@endphp

@extends('layouts.app')
@section('content')

<div class="cal_table_style">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule.menu_button' )

            <div class="card cal_table_style">
                <div class="card-header cal_table_style">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body cal_table_style">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @php
                        $today = Carbon::today(); 
                        $dates = ScheduleController::getMonthlyCalendarDates( $base_date );
                        $argvs = [ 'base_date' => '', 
                                   'dept_id'   => $request->dept_id,
                                   'users'     => $request->users,
                                   'search_mode' => $request->search_mode,
                                   ];
                    @endphp

                    <!-- カレンダー月表示　月切替ボタン -->
                    <div class="row m-2 w-100 w-md-50">
                        @php
                            $argv = ScheduleController::get_argv_for_forms( $request, $base_date->copy()->subMonth(1)->format('Y-m-d') );
                        @endphp
                        <a   class="col   col-md-1 links " href="{{ route( Route::currentRouteName(), $argv ) }}">
                            <i class="fas fa-caret-left" style="font-size: 21px; color: black;"></i></a>
                        <div class="col-6 col-lg-2 h4">{{ $base_date->year }}年 {{ $base_date->month }}月</div>

                        @php
                            $argv = ScheduleController::get_argv_for_forms( $request, $base_date->copy()->addMonth(1)->format('Y-m-d') );
                        @endphp
                        <a   class="col   col-md-1 links" href="{{ route( Route::currentRouteName(), $argv ) }}">
                            <i class="fas fa-caret-right" style="font-size: 21px; color: black;"></i></a>
                        
                        @if( $today->format( 'Ym' ) != $base_date->format( 'Ym' ) ) 
                            @php
                                $argv = ScheduleController::get_argv_for_forms( $request, $today->format('Y-m-d') );
                            @endphp
                            <a class="col-12 col-md-1 btn btn-sm btn-outline-secondary" href="{{ route( Route::currentRouteName(), $argv ) }}">今日</a>
                        @endif
                    </div>
                    
                    <!-- スケジュール検索モーダル -->
                    @include( 'groupware.schedule.index_parts_show_modal' )
                    
                    <!-- スケジュール表示 -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
                                    <th style="min-width:180px;max-width:14.3%;">{{ $dayOfWeek }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dates as $date)
                                @if ($date->dayOfWeek == 0)
                                    <tr>
                                @endif
                                @php
                                    $class="";
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
                                <td class="date_cell cal_cell_style {{ $class }} text-truncate" data-date="{{ $d }}">
                                    <div class=" show_daily schedule" data-date="{{ $d }}">
                                        {{ $date->day }}
                                    </div>
                                    @foreach( $s_ids as $id )
                                        @php 
                                            $s = $schedules->find($id); 
                                            if( $s->user->id == auth('user')->id() ) {
                                                $s_class = "mine";
                                                $style  = "background-color:". optional($s->schedule_type)->color;
                                                $style .= "; color:" . optional( $s->schedule_type )->text_color .";"; 
                                            } else {
                                                $s_class = "others";
                                                $style = "";
                                                $style  = "background-color:". optional($relation_schedule_type)->color;
                                                $style .= "; color:" . optional($relation_schedule_type)->text_color .";";
                                            }

                                            #dump( $style );
                                            if( $i >= 7 ) {
                                                printf( '<div class="show_daily schedule" data-date="%s">・・・</div>', $d );
                                                break;
                                             }
                                            $i++;
                                          
                                        @endphp
                                        
                                        <div class="show_m schedule {{ $s_class }} cal_cell" style='{{ $style }}' data-sid="{{ $s->id }}" data-date="{{ $d }}" id="{{ $d }}_{{ $s->id }}">
                                            【 {{ $s->start_time->format( 'H:i' ) }} 
                                             {{ $s->user->name }} 】
                                             {{ $s->name }}
                                        </div> 
                                    @endforeach
                                </td>
                                @if ($date->dayOfWeek == 6)
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>


                    <!-- クリック時　動作用フォーム・スクリプト -->
                    <div>

                        {{ Form::open( [ 'url' => route( 'groupware.schedule.daily' ), 'id' => 'daily_form' ]) }} 
                            @csrf
                            @method( 'GET' )
                            <input type=hidden name="base_date" id="daily_form_base_date">
                            <input type=hidden name="dept_id"    value="{{ $request->dept_id }}">
                            @if( isset( $request->users )) 
                                @foreach( optional($request)->users as $i => $user_id )
                                    <input type=hidden name="users[]" value="{{ $user_id }}">
                                @endforeach
                            @endif
    
                        {{ Form::close() }}
                        
                        {{ Form::open( [ 'url' => route( 'groupware.schedule.show_m' ), 'id' => 'show_m' ]) }} 
                            @csrf
                            @method( 'GET' )
                            <input type=hidden name="id" id="show_m_sid">
                            <input type=hidden name="base_date" id="show_m_base_date">
                            <input type=hidden name="dept_id"    value="{{ $request->dept_id }}">
                            @if( isset( $request->users )) 
                                @foreach( optional($request)->users as $i => $user_id )
                                    <input type=hidden name="users[]" value="{{ $user_id }}">
                                @endforeach
                            @endif
                        {{ Form::close() }}
                        
                        {{ Form::open( [ 'url' => route( 'groupware.schedule.create' ), 'id' => 'add_form' ]) }} 
                            @csrf
                            @method( 'GET' )
                            <input type=hidden name="start_time" id="add_form_start_time">
                            <input type=hidden name="dept_id"    value="{{ $request->dept_id }}">
                            @if( isset( $request->users )) 
                                @foreach( optional($request)->users as $i => $user_id )
                                    <input type=hidden name="users[]" value="{{ $user_id }}">
                                @endforeach
                            @endif
                        {{ Form::close() }}
                        <script type="text/javascript">
    
                            //　日次表示
                            //
                            $('.show_daily').click( function() {
                                 var date = $(this).data('date');
                                console.log( date );
                                 $('#daily_form_base_date').val( date );
                                 $('#daily_form').submit();
                            });
                        
                            //　スケジュールを表示
                            //
                            $('.show_m').click( function() {
                                // console.log( $(this).data('sid'));
                                // console.log( $(this).prop('id')); 
                                var sid = $(this).data('sid');
                                var date = $(this).data('date');
                                // console.log( sid );
                                // console.log( date );
                                $('#show_m_sid').val(sid);
                                $('#show_m_base_date').val(date);
                                
                                $('#show_m').submit();
                            });
                            
                            //　スケジュール新規作成
                            //
                            $('.date_cell').dblclick( function() {
                                 var date = $(this).data('date');
                                //  console.log( date );
                                 $('#add_form_start_time').val( date );
                                 $('#add_form').submit();
                            });
                            
                        </script>
                    </div>


                    <!-- ＣＳＶ出力ボタン -->                
                    <div>
                        <div class="w-100"></div>
                        @php
                            $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                        @endphp
            
                        {{ OutputCSV::button( [ 'route_name' => 'customer.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

