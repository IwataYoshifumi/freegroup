@extends('layouts.app')

@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use Illuminate\Support\Facades\Auth; 

use App\Http\Helpers\BackButton;

@endphp  

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
             <div class="container">
                @include( 'vacation.common.menu' )
            </div>
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    <div>          
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    </div>

                    @include( 'vacation.common.parts_find' )
                    
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>&nbsp;</th>
                            @if( ! empty( $find['show_item']['社員番号']     )) <th>社員番号</th>     @endif
                            @if( ! empty( $find['show_item']['部署']         )) <th>部署</th>         @endif
                            @if( ! empty( $find['show_item']['役職']         )) <th>役職</th>         @endif
                            <th>名前</th>
                            <th>休暇期間</th>
                            <th>休暇日数</th>
                            @if( ! empty( $find['show_item']['ステータス']   )) <th>ステータス</th>   @endif
                            @if( ! empty( $find['show_item']['休暇種別']     )) <th>休暇種別</th>     @endif
                            @if( ! empty( $find['show_item']['休暇理由']     )) <th>休暇理由</th>     @endif
                            
                        </tr>
                        @if( isset( $vacation ) and count( $vacation )) 
                            @foreach( $vacation as $v )
                                <tr>
                                    <td>
                                    <a class='btn btn-sm btn-success' href='{{ route( 'vacation.application.show', [ 'application' => $v->id ] ) }}'>詳細</a>
                                    </td>
                                    @if( ! empty( $find['show_item']['社員番号']     ))<td>{{ $v->code  }}</td>     @endif
                                    @if( ! empty( $find['show_item']['部署']         ))<td>{{ $v->dept  }}</td>     @endif
                                    @if( ! empty( $find['show_item']['役職']         ))<td>{{ $v->grade }}</td>     @endif
                                    <td>{{ $v->name  }}</td>
                                    <td>@if( $v->num <= 1 )
                                            {{ $v->start_date }}
                                        @else
                                            {{ $v->start_date }} ～ {{ $v->end_date }}
                                        @endif
                                    </td>
                                    <td>{{ Vacation::pnum( $v->num ) }}</td>
                                    @if( ! empty( $find['show_item']['ステータス']  ))<TD>{{ $v->status }}</TD>     @endif
                                    @if( ! empty( $find['show_item']['休暇種別']    ))<TD>{{ $v->type   }}</TD>     @endif
                                    @if( ! empty( $find['show_item']['休暇理由']    ))<td>{{ $v->reason }}</td>     @endif
                                </tr>
                            @endforeach

                        @endif
                    </table>
                
                    @if( isset( $vacation ) and count( $vacation )) 
                        {{ Form::open( ['url' => route( 'vacation.common.csv' ) , 'method' => 'get', 'id' => 'csv_form', 'target' => '_blank' ] ) }}
                            @csrf
                            @foreach( $find as $key => $value )
                                @if( ! empty( $value ))
                                    @if( ! is_array( $value ) ) 
                                        {{ Form::hidden( "find[$key]", $value  ) }}
                                    @else
                                        @foreach( $value as $i => $v )
                                            {{ Form::hidden( "find[$key][$i]", $v  ) }}
                                        @endforeach
                                    @endif
                                @endif
                            @endforeach
                        {{ Form::close() }}

                        <div class="container col-12">
                            <div class="row">
                                <div class="m-2">
                                     {{ $vacation->appends( ['find' => $find ] )->links() }}
                                </div>
                                <button type="button" class="col-4 col-lg-2 btn btn-warning m-2" onClick="csv_form_submit()">CSV出力</button>
                            </div>
                        </div>
                        <script>
                            function csv_form_submit() {
                                $('#csv_form').submit();
                            }
                        </script>
                    @endif
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@php

@endphp 

@endsection

