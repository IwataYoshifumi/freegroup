@extends('layouts.app')

@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Vacation;


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

                    @include( 'vacation.common.how_many_left_find' )
                    
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>&nbsp;</th>
                            <th>社員番号</th>
                            <th>部署</th>
                            <th>役職</th>
                            <th>名前</th>
                            <th>有給残日数</th>
                        </tr>
                        @if( isset( $vacation ) and count( $vacation )) 
                            @foreach( $vacation as $v ) 
                                <tr>
                                    <td>
                                    <a class='btn btn-sm btn-success' href='{{ route( 'vacation.user.detail', [ 'user' => $v->user_id ] ) }}'>詳細</a>
                                    </td>
                                    <td>{{ $v->code       }}</td>
                                    <td>{{ $v->dept_name  }}</td>
                                    <td>{{ $v->grade      }}</td>
                                    <td>{{ $v->user_name  }}</td>
                                    <td>{{ Vacation::pnum( $v->num ) }}</td>
                                </tr>
                            @endforeach

                        @endif
                    </table>
                
                    @if( isset( $vacation ) and count( $vacation )) 
                        {{ Form::open( ['url' => route( 'vacation.common.how_many_day.csv' ) , 'method' => 'get', 'id' => 'csv_form', 'target' => '_blank' ] ) }}
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

