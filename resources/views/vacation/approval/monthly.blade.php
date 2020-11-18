@extends('layouts.app')

@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use Illuminate\Support\Facades\Auth; 

use App\Http\Controllers\Vacation\CommonController;
use App\Http\Helpers\BackButton;

@endphp  

@section('content')

<div class="cal_table_style">
    <div class="row justify-content-center">
        <div class="col-md-12">
             <div class="container">
                @include( 'vacation.approval.menu' )
            </div>
            <div class="card cal_table_style">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    <div>          
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    </div>

                    @include( 'vacation.common.monthly_find' )
                  
                    <!-- カレンダー表示 -->  
                    @if( ! isset( optional( $request )->root_route )) 
                    
                        <!-- カレンダー月表示　月切替ボタン -->
                        <div class="row m-2 w-100 w-md-50">
                            @php
                                $today = Carbon::today(); 
                                $argv = CommonController::get_argv_for_forms( $request, $base_date->copy()->subMonth(1)->format('Y-m-d') );
                            @endphp
                            <a   class="col   col-md-1 links " href="{{ route( Route::currentRouteName(), $argv ) }}">
                                <i class="fas fa-caret-left" style="font-size: 21px; color: black;"></i></a>
                            <div class="col-6 col-lg-2 h4">{{ $base_date->year }}年 {{ $base_date->month }}月</div>
    
                            @php
                                $argv = CommonController::get_argv_for_forms( $request, $base_date->copy()->addMonth(1)->format('Y-m-d') );
                            @endphp
                            <a   class="col   col-md-1 links" href="{{ route( Route::currentRouteName(), $argv ) }}">
                                <i class="fas fa-caret-right" style="font-size: 21px; color: black;"></i></a>
                            
                            @if( $today->format( 'Ym' ) != $base_date->format( 'Ym' ) ) 
                                @php
                                    $argv = CommonController::get_argv_for_forms( $request, $today->format('Y-m-d') );
                                @endphp
                                <a class="col-12 col-md-1 btn btn-sm btn-outline-secondary" href="{{ route( Route::currentRouteName(), $argv ) }}">今日</a>
                            @endif
                        </div>
                    
                        <!-- カレンダー表示 -->
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
                                    if( array_key_exists( $d, $vacation_ids )) {
                                        $s_ids = $vacation_ids[$d]; 
                                    }
                                    $i = 1;
                                @endphp
                                <td class="date_cell cal_cell_style {{ $class }} text-truncate" data-date="{{ $d }}">
                                    <div data-date="{{ $d }}">
                                        {{ $date->day }}
                                    </div>
                                    @foreach( $s_ids as $id )
                                        @php 
                                            $v = $vacations->find($id);
                                            if( $v->status == "承認待ち" ) {
                                                $class = "alart-primary text-primary font-weight-bold";
                                            } elseif( $v->status == "承認" ) {
                                                $class = "alart-success text-success font-weight-bold";
                                            } elseif( $v->status == "休暇取得完了" ) {
                                                $class = "";
                                            } elseif( $v->status == "却下" ) {
                                               $class = "text-danger font-weight-bold";
                                            } else {
                                               $class = "text-secondary";
                                            }

                                            if( $i >= 7 ) {
                                                printf( '<div class="show_daily schedule" data-date="%s">・・・</div>', $d );
                                                break;
                                             }
                                            $i++;
                                            
                                          
                                        @endphp

                                        <div class="show_m schedule text-nowrap {{ $class }}" data-vid="{{ $v->id }}">
                                            @if( in_array( '部署名', $find['show'] )) 
                                                {{ $v->user->department->name }}
                                            @endif
                                            {{ $v->user->name }}
                                            @if( in_array( '休暇種別', $find['show'] ))
                                                {{ $v->type   }}
                                            @endif
                                            @if( in_array( 'ステータス', $find['show'] ))
                                                {{ $v->status }}
                                            @endif
                                            @if( in_array( '休暇理由', $find['show'] ))
                                                {{ $v->reason }}
                                            @endif
                                        </div> 
                                        
                                        
                                    @endforeach
                                </td>
                                @if ($date->dayOfWeek == 6)
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    
                    @endif
                    
                    <!-- クリック時　動作用フォーム・スクリプト -->
                    <div>
                        {{ Form::open( [ 'url' => route( 'vacation.application.show_m' ), 'id' => 'show_m' ]) }} 
                            @csrf
                            @method( 'GET' )
                            <input type=hidden name="application" id="show_m_application">
                        {{ Form::close() }}
                        
                        <script type="text/javascript">
    
                            //　スケジュールを表示
                            //
                            $('.show_m').click( function() {
                                // console.log( $(this).data('sid'));
                                // console.log( $(this).prop('id')); 
                                var vid = $(this).data('vid');
                                console.log( vid );
                                // console.log( date );
                                $('#show_m_application').val(vid);
                                $('#show_m').submit();
                            });
                            

                        </script>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@php

@endphp 

@endsection

