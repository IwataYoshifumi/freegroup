@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\Schedule\Models\Schedule;
use App\myHttp\GroupWare\Controllers\ScheduleController;

use App\Models\Vacation\Dept;

#dump( Request::all() );
#dump( session( 'back_button' ) );

$users     = ( isset( $request->users )) ? $request->users : [];

$max_rows = 7;

@endphp

@extends('layouts.app')


@section('content')

<div class="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @php
                        $today = Carbon::today(); 
                    @endphp

                    <div class="row m-2 w-100 w-md-50">
                        @php
                            $argv_1 = ScheduleController::get_argv_for_forms( $request, $base_date->copy()->subDays(7)->format('Y-m-d') );
                            $argv_2 = ScheduleController::get_argv_for_forms( $request, $base_date->copy()->addDays(7)->format('Y-m-d') );
                            
                            if( Arr::first( $dates )->month == Arr::last( $dates )->month ) {
                                $print_period =  Arr::first( $dates )->format( 'Y年n月j' )."～".Arr::last( $dates )->day."日"; 
                            } else {
                                $print_period = Arr::first( $dates )->format( 'Y年n月j日' )."～".Arr::last( $dates )->format( 'n月j日' );
                            }
                        @endphp
                        
                        <a   class="col-1 col-md-1 links" href="{{ route( Route::currentRouteName(), $argv_1 ) }}">
                                    <i class="fas fa-caret-left" style="font-size: 21px; color: black;"></i></a>

                        <div class="col-9 col-lg-2 text-nowrap h4">
                            {{ $print_period }}
                        </div>

                        <a   class="col-1 col-md-1 links" href="{{ route( Route::currentRouteName(), $argv_2 ) }}">
                                    <i class="fas fa-caret-right" style="font-size: 21px; color: black;"></i></a>
                                    
                        @if( Arr::first( $dates )->gt( $today ) or Arr::last( $dates )->lt( $today )) 
                            <a class="col-12 col-md-1 btn btn-sm btn-outline-secondary" href="{{ route( Route::currentRouteName(), [ 'base_date' => $today->format('Y-m-d') ] ) }}">今日</a>
                        @endif

                    </div>

                    @include( 'groupware.schedule.index_parts_show_modal' )

                    <!------------------------------------------------------------------------------------------>
                    <!-- ＰＣ用表示                                                                           -->
                    <!------------------------------------------------------------------------------------------>
                    <div class="d-none d-lg-block">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    @php
                                        $days = ['日', '月', '火', '水', '木', '金', '土'];
                                    
                                    @endphp
                                    
                                    @foreach ( $dates as $date )
                                        <th style='text-align:center width:14.2%' class="bg-light">
                                            <div class=" show_daily schedule" data-date="{{ $date }}">
                                                {{ $date->format( 'n/j' ) }}【{{ $days[ $date->format( 'w' ) ] }}】
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach ($dates as $date)
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
                                        <td class="date_cell {{ $class }}" data-date="{{ $d }}">
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
                                                    
                                                    
                                                    
                                                    if( $i >= $max_rows ) {
                                                        printf( '<div class="show_daily schedule" data-date="%s">・・・</div>', $d );
                                                        break;
                                                    }
                                                    $i++;
                                                @endphp
                                                <div class="show_m schedule {{ $s_class }}" style="{{ $style }}" data-sid="{{ $s->id }}" data-date="{{ $d }}" id="{{ $d }}_{{ $s->id }}">
                                                    {{ $s->p_time() }}
                                                    <!--
                                                    {{ $s->user->name }} : 
                                                    {{ $s->start_time->format( 'H:i' ) }}
                                                    {{ $s->name }}
                                                    -->
                                                </div> 
                                            @endforeach
                                        </td>
                                    @endforeach
                                <tr>
                            </tbody>
                        </table>                                    
                    </div>

                    <div class="col-12">aa</div>
                    
                    <!------------------------------------------------------------------------------------------>
                    <!-- スマホ表示                                                                           -->
                    <!------------------------------------------------------------------------------------------>
                    <div class="d-block d-lg-none">   
                        <table class="table table-bordered">
                            <tbody class="">                                
                                @foreach ($dates as $date)
                                    @php
                                        $class="";
                                        if($date->month != $base_date->month ) { $class="bg-light"; }
                                        if( $date->eq( $base_date )) { $class.=" border border-primary"; }
                                        $d = $date->format( 'Y-m-d' );
                                        setlocale(LC_ALL, 'ja_JP.UTF-8');
                                        $print_date = $date->formatLocalized('%Y-%m-%d ( %a )');
    
                                        //　日付内にスケジュールがあるか確認
                                        //
                                        $s_ids = [];
                                        if( array_key_exists( $d, $schedule_ids )) {
                                            $s_ids = $schedule_ids[$d]; 
                                        }
                                        $i = 1;
                                    @endphp
                                    
                                    @if( count( $s_ids )) 
                                    <tr>
                                        <td class="date_cell" data-date="{{ $d }}">
                                            <div class="cal_cell_header_style show_daily schedule bg-light" data-date="{{ $date }}">{{ $print_date }}</div>
                                            <br>
                                            @foreach( $s_ids as $id )
                                                @php 
                                                    $s = $schedules->find($id); 
                                                    if( $s->user->id == auth('user')->id() ) {
                                                        $s_class = "mine";
                                                    } else {
                                                        $s_class = "others";
                                                    }
                                                    if( $i >= $max_rows ) {
                                                        printf( '<div class="show_daily schedule" data-date="%s">・・・</div>', $d );
                                                        break;
                                                    }
                                                    $i++;
                                                @endphp
                                                <div class="show_m schedule {{ $s_class }}" data-sid="{{ $s->id }}" data-date="{{ $d }}" id="{{ $d }}_{{ $s->id }}">
                                                    {{ $s->p_time() }}
                                                    <!--
                                                    {{ $s->user->name }} : 
                                                    {{ $s->start_time->format( 'H:i' ) }}
                                                    {{ $s->name }}
                                                    -->
                                                </div> 
                                            @endforeach
                                        </td>
                                    </tr class="d-block d-lg-none">
                                    @else
                                        <tr>
                                            <td class="bg-light">
                                                <div class="show_daily schedule" data-date="{{ $date }}">{{ $print_date }}</div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- フォーム -->
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
                    </div>
                    
                    <!-- スクリプト -->
                    <script type="text/javascript">
                    
                        //　日次表示
                        //
                        $('.show_daily').click( function() {
                             var date = $(this).data('date');
                            //  console.log( date );
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
                             console.log( date );
                             $('#add_form_start_time').val( date );
                             $('#add_form').submit();
                        });
                        
                    </script>


                    <div class="w-100"></div>
                    @php
                    #    $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                        $inputs = [];
                    @endphp
        
                    {{ OutputCSV::button( [ 'route_name' => 'customer.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

