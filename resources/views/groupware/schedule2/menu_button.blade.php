@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;
use App\myHttp\GroupWare\Models\Schedule;
use App\Http\Helpers\ScreenSize;

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">


    @if( $auth->can( 'create', Schedule::class ))
        @if( ! ScreenSize::isMobile() )
            <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
                <span class="d-block d-lg-none">新規</span>
                <span class="d-none d-lg-block">新規予定</span>
            </a>
        @endif
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

    @if( ! ScreenSize::isMobile() )
        <a class="btn btn-menu m-1" href="{{ route( 'groupware.show_all.index'  ) }}">
            <div class="">予定検索</div>
        </a>
    @endif
    
    <a class="btn btn-menu m-1" href="{{ route( 'groupware.show_all.monthly'  ) }}">
        <div class="">
            <span class="">月表示</span>
        </div>
    </a>
        
    <a class="btn btn-menu  m-1" href="{{ route( 'groupware.show_all.weekly'  ) }}">
            <span class="">週表示</span>
    </a>

    <a class="d-none d-lg-block btn btn-menu m-1" href="{{ route( 'groupware.calendar.index', $args   ) }}">
        <div class="">カレンダー設定</div>
    </a>
        
</div>
