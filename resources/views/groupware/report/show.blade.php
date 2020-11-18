@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

if( ! isset( $customer ) ) { $customer = null; }

$users     = $report->users;
$user      = $report->user;
$files     = $report->files;
$customers = $report->customers;
$schedules = $report->schedules;

# dump( $schedules );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'groupware.report.show_button' )
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'report' => optional($report)->id ] ) ]) }}
                        @method( 'GET' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $report )->id ) }}
                        
                        @include( 'groupware.report.show_parts' ) 
                        
                        <div class="col-12">
                            {{ BackButton::form() }}

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
