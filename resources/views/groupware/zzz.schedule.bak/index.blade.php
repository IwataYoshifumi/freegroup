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
            @include( 'groupware.schedule.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <hr>
                    <table>


                    </table>
                    <div class="w-100"></div>
                    @php
                        $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                    @endphp
        
                    {{ OutputCSV::button( [ 'route_name' => 'customer.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

