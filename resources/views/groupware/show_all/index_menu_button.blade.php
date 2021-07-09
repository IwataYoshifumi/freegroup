@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\Schedule2IndexController;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Task;

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">

    @if( is_array( $request->calendars ) and count( $request->calendars ))
        @if( $auth->can( 'create', Schedule::class )) 
            <a class="btn btn-primary col-2 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
                <span class="">新規スケジュール</span>
            </a>
        @else
            @php
            $route = route( 'groupware.calendar.index' );
            $title = "予定作成権限のあるカレンダーがありません。まずはカレンダーを作成してください";
            @endphp
            <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">新規カレンダー作成</a>
        @endif
    @endif

    @if( is_array( $request->tasklists ) and count( $request->tasklists ))
        @if( $auth->can( 'create', Task::class ))
            <a class="btn btn-primary col-2 m-1" href="{{ route( 'groupware.task.create'   ) }}">
                <span class="">新規タスク</span>
            </a>
        @else
            @php
            $route = route( 'groupware.tasklist.index' );
            $title = "タスク作成権限のあるタスクリストがありません。まずはタスクリストを作成してください";
            @endphp
            <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">新規タスクリスト作成</a>
        @endif
    @endif

    @if( isset( $request->report_lists ))
        @if( $auth->can( 'create', Report::class ) and count( $request->report_lists ))
            <a class="btn btn-primary col-2 m-1" href="{{ route( 'groupware.report.create'   ) }}">
                <span class="">新規日報</span>
            </a>
        @else
            @if( ! count( $request->calendars ) and ! count( $request->tasklists ) and ! count( $request->report_lists )) 
                @php
                $route = route( 'groupware.report_list.index' );
                $title = "日報作成権限のある日報リストがありません。まずは日報リストを作成してください";
                @endphp
                <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">新規日報リスト作成</a>
            @endif
        @endif
    @endif
        
</div>
