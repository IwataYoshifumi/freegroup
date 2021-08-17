@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Reservation;

if( ! isset( $customer ) ) { $customer = null; }

$route_name = Route::currentRouteName();

$facility = $reservation->facility;
$auth      = auth( 'user' )->user();
#$users     = $reservation->users;

# dd( $user, $files );

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
                    
                    @if( $facility->is_disabled() )
                        <div class="alert-warning m-1 p-2">この設備は設備管理者によって無効化されました。編集・新規追加はできません</div>
                    @endif
                    
                    @include( 'groupware.reservation.show_parts' ) 
                        
                    <div class="col-12">
                        {{ BackButton::form() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $( function() { $('.uitooltip').uitooltip(); });
</script>


@endsection

