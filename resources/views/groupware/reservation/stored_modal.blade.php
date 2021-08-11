@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Search\GetAccessLists;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\SubClass\GetFacilityForReservationInput;


// if_debug( $reservation );

//　初期化
//
$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

@endphp

@include('layouts.header')

<div class="container">

    @include( 'layouts.flash_message' )
    @include( 'layouts.error' )
  
    <div class="row">
        <div class="col-12">&nbsp;</div>
        
        @foreach( $reservations as $reservation )
            @if( $loop->first )
                <div class="col-3">利用目的</div>
                <div class="col-9">{{ $reservation->purpose }}</div>
                <div class="col-3">予約期間</div>
                <div class="col-9">{{ $reservation->p_time() }}</div>
            @endif
            @php
            $style = $reservation->style();
            @endphp

            <div class="col-3">予約設備</div>
            <div class="col-9" style="{{ $style }}">{{ $reservation->facility->name }}
            </div>
        @endforeach
    </div>
    

</div>