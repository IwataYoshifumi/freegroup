@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

$route_name =  Route::currentRouteName();
//$route_name = 'groupware.file.test.update';

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include( 'groupware.file.menu_button' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body w-100">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <div>
                        {{ Form::open( [ 'route' => $route_name, 'method' => 'POST' ] )}}
                            @csrf
                            <x-input_files2 :input="$component_input_files" />
                        
                            <button type='submit'>送信</button>
                        {{ Form::close() }}
                    </div>
                    <div class="col-12">
                        {{ BackButton::form() }}
                    </div>
    
                </div>
            </div>
        </div>
    </div>
@endsection
