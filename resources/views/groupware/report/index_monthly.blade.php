@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\Schedule\Models\Schedule;
use App\myHttp\GroupWare\Controllers\ScheduleController;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@extends('layouts.app')


@section('content')

<div class="container">
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
                        $dates = ScheduleController::getMonthlyCalendarDates( $base_date );
                    @endphp

                    <div class="row m-2 w-100 w-md-50">
                        
                        <a   class="col   col-md-1 links " href="{{ route( Route::currentRouteName(), [ 'base_date' => $base_date->copy()->subMonth(1)->format('Y-m-d') ] ) }}">&lt;&lt;　&nbsp;</a>
                        <div class="col-6 col-md-2 h4">{{ $base_date->year }}年 {{ $base_date->month }}月</div>
                        <a   class="col   col-md-1 links" href="{{ route( Route::currentRouteName(), [ 'base_date' => $base_date->copy()->addMonth(1)->format('Y-m-d') ] ) }}">&nbsp;　&gt;&gt;</a>
                        @if( $today->format( 'Ym' ) != $base_date->format( 'Ym' ) ) 
                            <a class="col-12 col-md-2 btn btn-outline-secondary h4" href="{{ route( Route::currentRouteName(), [ 'base_date' => $today->format('Y-m-d') ] ) }}">今日</a>
                        @endif
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
                                    <th>{{ $dayOfWeek }}</th>
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
                                @endphp
                                <td class="date_cell {{ $class }}" data-date="{{ $d }}">
                                    {{ $date->day }}
                                    @foreach( $s_ids as $id )
                                        @php 
                                            $s = $schedules->find($id); 
                                            if( $s->user->id == auth('user')->id() ) {
                                                $s_class = "mine";
                                            } else {
                                                $s_class = "others";
                                            }
                                        
                                        @endphp
                                        <div class="show_m schedule {{ $s_class }}" data-sid="{{ $s->id }}" id="{{ $d }}_{{ $s->id }}">
                                            {{ $s->p_time() }}
                                            <!--
                                            {{ $s->user->name }} : 
                                            {{ $s->start_time->format( 'H:i' ) }}
                                            {{ $s->name }}
                                            -->
                                        </div> 
                                    @endforeach
                                </td>
                                @if ($date->dayOfWeek == 6)
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>


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



@endsection