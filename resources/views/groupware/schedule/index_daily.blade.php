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
                    @endphp

                    <div class="row m-2 w-100 w-md-50">
                        <a   class="col   col-md-1 links" href="{{ route( Route::currentRouteName(), [ 
                                        'base_date' => $base_date->copy()->subDay()->format('Y-m-d'),
                                        'dept_id'   => $request->dept_id,
                                        'users'     => $request->users,
                                    ] ) }}"><i class="fas fa-caret-left" style="font-size: 21px; color: black;"></i></a>

                        <div class="col-6 col-md-2 h4">

                            {{ $base_date->format( 'Y-n-j' ) }}
                        </div>

                        <a   class="col   col-md-1 links" href="{{ route( Route::currentRouteName(), [ 
                                        'base_date' => $base_date->copy()->addDay()->format('Y-m-d'), 
                                        'dept_id'   => $request->dept_id,
                                        'users'     => $request->users,
                                    ] ) }}"><i class="fas fa-caret-right" style="font-size: 21px; color: black;"></i></a>
                        @if( $base_date->diffInDays( $today ) >=1 ) 
                            <a class="col-12 col-md-2 btn btn-sm btn-outline-secondary" href="{{ route( Route::currentRouteName(), [ 'base_date' => $today->format('Y-m-d') ] ) }}">今日</a>
                        @endif
                    </div>

                    @include( 'groupware.schedule.index_parts_show_modal' )
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style='text-align:center'>{{ $base_date->format( 'Y年n月j日' ) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="date_cell">
                                    @foreach( $schedules as $s )
                                        @php 
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
                                        
                                        @endphp
                                        <div class="show_m schedule {{ $s_class }}" style="{{ $style }}"data-sid="{{ $s->id }}">
                                            {{ $s->p_time() }}
                                            <!--
                                            {{ $s->user->name }} : 
                                            {{ $s->start_time->format( 'H:i' ) }}
                                            {{ $s->name }}
                                            -->
                                        </div> 
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    {{ Form::open( [ 'url' => route( 'groupware.schedule.show_m' ), 'id' => 'show_m' ]) }} 
                        @csrf
                        @method( 'GET' )
                        <input type=hidden name="id" id="show_m_sid">
                        <input type=hidden name="base_date" id="show_m_base_date">
                    {{ Form::close() }}
                    
                    {{ Form::open( [ 'url' => route( 'groupware.schedule.create' ), 'id' => 'add_form' ]) }} 
                        @csrf
                        @method( 'GET' )
                        <input type=hidden name="start_time" id="add_form_start_time">
                    {{ Form::close() }}
                    <script type="text/javascript">
                    
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

