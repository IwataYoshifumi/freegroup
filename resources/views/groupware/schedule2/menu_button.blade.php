@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;

@endphp

<div class="row m-1 w-100 container">

    <a class="btn btn-primary col-2 col-lg-1 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
        <div class="">新規予定</div>
    </a>
    @php
        if( isset( $request )) {
            #dump( $request->all() );
            $args = Schedule2IndexController::get_argv_for_forms( $request, $request->base_date );
        } else {
            $args = [];
        }
        if( preg_match(  '/schedule\.show/', Route::currentRouteName() )) {
            $args['base_date'] = $schedule->start->format( 'Y-m-d' );
        }
        
    @endphp
    <a class="btn btn-menu  m-1" href="{{ route( 'groupware.schedule.index', $args  ) }}">
        <div class="">リスト</div>
    </a>
    
    <a class="btn btn-menu  m-1" href="{{ route( 'groupware.schedule.monthly', $args  ) }}">
        <div class="">月次</div>
    </a>
        
    <a class="btn btn-menu  m-1" href="{{ route( 'groupware.schedule.weekly', $args   ) }}">
        <div class="">週次</div>
    </a>

    <a class="btn btn-menu m-1" href="{{ route( 'groupware.calendar.index', $args   ) }}">
        <div class="">カレンダー設定</div>
    </a>
        
    @if( is_debug() )
        <a class="btn btn-menu btn-secondary m-1 disabled" href="">
            <div class="">空き時間</div>
        </a>
    @endif
    
</div>
