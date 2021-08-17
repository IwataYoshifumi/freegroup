@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;
use App\myHttp\GroupWare\Models\Schedule;

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">

    @if( $auth->can( 'create', Reservation::class )) 
        <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.reservation.create'   ) }}">
            <span class="">設備予約</span>
        </a>
    @endif
    <a class="btn btn-menu m-1" href="{{ route( 'groupware.reservation.monthly'  ) }}">
        <div class="">設備予約状況</div>
    </a>
</div>
