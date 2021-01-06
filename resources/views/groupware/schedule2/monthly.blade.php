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

$users     = ( isset( $request->users )) ? $request->users : [];

$row_num = 6;

@endphp

@extends('layouts.app')
@section('content')

<div class="cal_table_style">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule2.menu_button' )

            <div class="card cal_table_style">
                <div class="card-header cal_table_style">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body cal_table_style">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @php
                        $today = Carbon::today(); 
                        $argvs = [ 'base_date' => '', 
                                   'dept_id'   => $request->dept_id,
                                   'users'     => $request->users,
                                   'search_mode' => $request->search_mode,
                                   ];
                    @endphp

                    <!-- カレンダー月表示　月切替ボタン -->
                    <div class="row m-2 w-100 w-md-50">
                        {{ $base_date->format( 'Y-m-d' ) }}
                    </div>
                    
                    <!-- スケジュール検索モーダル -->
                    @include( 'groupware.schedule2.index_parts_show_modal' )
                    
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
                                    // dump( $date->month );
                                    $class = "";
                                    $d = $date->format( 'Y-m-d' );
                                    if($date->month != $base_date->month ) { $class="bg-light"; }
                                    
                                @endphp
                                <td class="date_cell cal_cell_style text-truncate {{ $class }}" data-date="{{ $d }}">
                                    <div class=" show_daily schedule" data-date="{{ $d }}">
                                        {{ $date->day }}<br>
                                        @php
                                            $s_ids = [];
                                            $d = $date->format( 'Y-m-d' );
                                            $s_ids = ( array_key_exists( $d, $schedule_ids )) ? $schedule_ids[$d] : [];
                                            $count = count( $s_ids );
                                        @endphp
                                        @foreach( $s_ids as $i => $id ) 
                                            @php
                                                $s = $schedules->find( $id );
                                                $calprop = $s->calprop();
                                            @endphp 
                                            @if( $i <= $row_num - 1 ) 
                                                <span style="{{ $calprop->style() }}">
                                                    {{ $s->p_time() }} : {{ $s->name }} @if( is_debug() ) ( {{ $s->id }} ) @endif 
                                                </span><br>
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




@php


@endphp




@endsection

