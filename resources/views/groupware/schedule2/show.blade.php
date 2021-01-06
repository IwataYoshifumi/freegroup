@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

if( ! isset( $customer ) ) { $customer = null; }

$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

$users     = $schedule->users;
$creator   = $schedule->creator;
$updator   = $schedule->updator;
$calendar  = $schedule->calendar;

$files     = $schedule->files;
$customers = $schedule->customers;
$reports   = $schedule->reports;

# dd( $user, $files );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'groupware.schedule2.show_button' )
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'schedule' => optional($schedule)->id ] ) ]) }}
                        @method( 'GET' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $schedule )->id ) }}
                        
                        @include( 'groupware.schedule2.show_parts' ) 
                        
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
