@php
use App\myHttp\Schedule\Models\Schedule;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.report.index_find' )
                    
                    <!-- 一覧フォーム -->
                    @php
                        $columns_name = [ '', '日報作成日時', '報告者', '件名', ];
                    @endphp
                    
                    <div class="m-1 p-1 border clearfix">
                        <div class="row">
                            @foreach( $columns_name as $name ) 
                                <div class="col">{{ $name }}</div>
                            @endforeach
                        </div>
                        
                        @foreach( $reports as $report )
                            <div class="row">
                                <div class="col">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route( 'groupware.report.show', [ 'report' => $report->id ] ) }}">詳細</a>
                                </div>
                                <div class="col">{{ $report->created_at->format( 'Y-m-d H:i' ) }}</div>
                                <div class="col">{{ $report->user->name     }}</div>
                                <div class="col">{{ $report->name           }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="w-100"></div>
                    @if( count( $reports )) 
                        @php
                            $inputs = [ 'find' => $find, 'search_mode' => $request->search_mode ];
                        @endphp
        
                        {{ OutputCSV::button( [ 'route_name' => 'groupware.report.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

