@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;
use App\myHttp\GroupWare\Models\Schedule;

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">

    @if( $auth->can( 'create', Schedule::class )) 
        <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
            <span class="d-block d-lg-none">新規</span>
            <span class="d-none d-lg-block">新規予定</span>
        </a>
    @else
        @php
            $route = route( 'groupware.calendar.index' );
            $title = "予定作成権限のあるカレンダーがありません。まずはカレンダーを作成してください";
        @endphp
    
        <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">新規カレンダー作成</a>
    @endif
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
    <a class="btn btn-menu m-1" href="{{ route( 'groupware.schedule.index', $args  ) }}">
        <div class="">リスト</div>
    </a>
    
    <a class="btn btn-menu m-1" href="{{ route( 'groupware.schedule.monthly', $args  ) }}">
        <div class="">
            <span class="d-block d-lg-none">月</span>
            <span class="d-none d-lg-block">月次</span>
        </div>
    </a>
        
    <a class="btn btn-menu  m-1" href="{{ route( 'groupware.schedule.weekly', $args   ) }}">
            <span class="d-block d-lg-none">週</span>
            <span class="d-none d-lg-block">週次</span>
    </a>

    <a class="d-none d-lg-block btn btn-menu m-1" href="{{ route( 'groupware.calendar.index', $args   ) }}">
        <div class="">カレンダー設定</div>
    </a>
        
</div>
