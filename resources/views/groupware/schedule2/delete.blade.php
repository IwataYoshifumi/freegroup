@extends('layouts.app')

@php

if( ! isset( $customer ) ) { $customer = null; }

use App\Http\Helpers\BackButton;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

if( ! isset( $customer ) ) { $customer = null; }

$customers = $schedule->customers;
$users     = $schedule->users;
$user      = $schedule->user;
$files     = $schedule->files;
$reports   = $schedule->reports;
$calendar  = $schedule->calendar;

$route_name = Route::currentRouteName();

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}( schedule_id {{ $schedule->id }} )</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @if( $route_name == "groupware.schedule.delete" )
                        <div class="alert alert-danger">スケジュールを削除します。よろしいですか。</div>
                    @elseif( $route_name == "groupware.schedule.deleted" ) 
                        <div class="alert alert-warning">スケジュールを削除しました。</div>
                    @endif
                    
                    
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'schedule' => optional($schedule)->id,  ]), 'name' => 'delete_form' ] ) }}
                        @method( 'DELETE' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $schedule )->id ) }}
                        
                        @include( 'groupware.schedule2.show_parts' )
                        
                        
                        <div class="col-12">

                            @if( preg_match( '/schedule\.delete$/', Route::currentRouteName() ) )
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
