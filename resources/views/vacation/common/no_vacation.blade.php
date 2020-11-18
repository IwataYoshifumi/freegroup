@extends('layouts.app')

@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
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
                            <th>社員番号</th> 
                            <th>部署</th>
                            <th>役職</th>
                            <th>名前</th>
                        </tr>
                        @if( isset( $users ) and count( $users )) 
                            @foreach( $users as $user ) 
                                <tr>
                                    <td>
                                    <a class='btn btn-sm btn-success' href='{{ route( 'vacation.user.detail', [ 'user' => $user->id ] ) }}'>詳細</a>
                                    </td>
                                    <td>{{ $user->code  }}</td>
                                    <td>{{ $user->department->name  }}</td>
                                    <td>{{ $user->grade }}</td>
                                    <td>{{ $user->name }}</td>
            
                                </tr>
                            @endforeach

                        @endif
                    </table>
                
                    @if( isset( $users ) and count( $users )) 
                        {{ Form::open( ['url' => route( 'vacation.common.no_vacation.csv' ) , 'method' => 'get', 'id' => 'csv_form', 'target' => '_blank' ] ) }}
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
                                    {{ $users->appends( ['find' => $find ] )->links() }}
                                </div>
                                <button type="button" class="col-4 col-lg-2 btn btn-warning m-2" onClick="csv_form_submit()">CSV出力</button>
                                <script>
                                    function csv_form_submit() {
                                        $('#csv_form').submit();
                                    }
                                </script>
                            </div>
                        </div>
                        
                    @endif
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@php

@endphp 

@endsection

