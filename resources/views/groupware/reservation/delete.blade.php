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
                    
                    <div class="col-12 alert-danger m-2 p-2">この設備予約をキャンセルします。よろしいですか。</div>
                    
                    @include( 'groupware.reservation.show_parts' ) 

                    {{ Form::open( [ 'url' => route( 'groupware.reservation.delete', [ 'reservation' => $reservation->id ]  ), 'method' => 'POST', 'id' => 'cancel_form' ] ) }}
                        @csrf
                    {{ Form::close() }}
                    
                    <div class="col-12">
                        <a class="btn btn-danger text-white" onClick='cancel_form_submit()'>予約キャンセル</a>
                        <script>
                            function cancel_form_submit() {
                                $('#cancel_form').submit();
                            }
                            
                        </script>
                        
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

