@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

$user      = $file->user;
$users     = $file->users;
$schedules = $file->schedules;
$reports   = $file->reports;

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body w-100">
                    @include( 'groupware.file.detail_button' )
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'groupware.file.detail_show' )

                  
                    </div>
                    <div class="col-12">
                        {{ BackButton::form() }}
                    </div>
    
                </div>
            </div>
        </div>
    </div>
@endsection
