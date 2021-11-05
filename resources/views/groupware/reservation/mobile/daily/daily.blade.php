@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Dept;
use App\Http\Helpers\BackButton;


$route_name = Route::currentRouteName();

@endphp

@extends('layouts.app_dialog')
@section('content')

<div style="width: 100%;" class="shadow">
    @include( 'groupware.reservation.mobile.daily.daily_button' )
</div>

<div style="width: 100%;" class="">
    @include( 'groupware.reservation.mobile.daily.daily_body' )
</div>
                
@endsection
