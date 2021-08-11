@php
use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Report;

$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

$facility = $reservation->facility;

@endphp

@include('layouts.header')

<div class="container">
    <div class="row">
        
        @include( 'groupware.reservation.show_parts' ) 
    </div>
</div>

