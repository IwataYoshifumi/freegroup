@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

use App\Http\Helpers\ScreenSize;

$schedules = $user->schedules->load( 'user' );
$reports   = $user->reports->load( 'user' );

# dump( $schedules, $reports );


@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'groupware.user.detail_parts' )
                    
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">

                            {{ BackButton::form() }}

                        </div>
                    </div>
                </div>
            </div>
            
            @include( 'groupware.user.detail_schedules' )
            
            @include( 'groupware.user.detail_reports' )



        </div>
    </div>
</div>

{{ ScreenSize::rendarScriptToGetScreenSize() }}

@endsection
