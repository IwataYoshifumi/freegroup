@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

if( ! isset( $customer ) ) { $customer = null; }

$users     = $report->users;
$user      = $report->user;
$files     = $report->files;
$customers = $report->customers;
$schedules = $report->schedules;

$route_name = Route::currentRouteName();

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}( report_id {{ $report->id }} )</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @if( $route_name == "groupware.report.delete" )
                        <div class="alert alert-danger">日報を削除します。よろしいですか。</div>
                    @elseif( $route_name == "groupware.report.deleted" ) 
                        <div class="alert alert-warning">日報を削除しました。</div>
                    @endif
                    
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'report' => optional($report)->id,  ]), 'name' => 'delete_form' ] ) }}
                        @method( 'DELETE' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $report )->id ) }}
                        
                        @include( 'groupware.report.show_parts' )
                        
                        
                        <div class="col-12">

                            @if( preg_match( '/report\.delete$/', Route::currentRouteName() ) )
                                <a class="btn btn-danger text-white" onClick="document.delete_form.submit()">削除実行</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
