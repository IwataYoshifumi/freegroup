@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\ScheduleController;

@endphp

<div class="row m-1 w-100 container">

    <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
        <div class="d-block d-lg-none">新規</div>
        <div class="d-none d-lg-block">新規予定作成</div>
    </a>
    @php
        if( isset( $request )) {
            #dump( $request->all() );
            $args = ScheduleController::get_argv_for_forms( $request, $request->base_date );
        } else {
            $args = [];
        }
        if( preg_match(  '/schedule\.show/', Route::currentRouteName() )) {
            $args['base_date'] = $schedule->start->format( 'Y-m-d' );
        }
        
    @endphp
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.index', $args  ) }}">
        <div class="d-block d-lg-none">一覧</div>
        <div class="d-none d-lg-block">一覧表示</div>
    </a>
    
    
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.monthly', $args  ) }}">
        <div class="d-block d-lg-none">月次</div>
        <div class="d-none d-lg-block">月次表示</div>
    </a>
        
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.weekly', $args   ) }}">
        <div class="d-block d-lg-none">週次</div><div class="d-none d-lg-block">週次表示</div>
    </a>
        
    <a class="btn col-1 m-1 ml-auto" href="{{ route( 'groupware.schedule.type.index'    ) }}">
        <i class="fas fa-cog" style="font-size: 21px; color: black;"></i>
    </a>
    

</div>
