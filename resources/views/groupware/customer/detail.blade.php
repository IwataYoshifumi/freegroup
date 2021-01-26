@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

$schedules = $customer->schedules()->orderBy( 'start_date' )->get();
$reports   = $customer->reports()->orderBy( 'start_date' )->get();

# dump( $schedules, $reports );


@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'customer.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'groupware.customer.detail_button' )
                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )


                    @include( 'groupware.customer.detail_parts' )
                    
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">

                            {{ BackButton::form() }}

                        </div>
                    </div>
                </div>
            </div>
            
            @include( 'groupware.customer.detail_schedules' )
            
            @include( 'groupware.customer.detail_reports' )



        </div>
    </div>
</div>
@endsection
