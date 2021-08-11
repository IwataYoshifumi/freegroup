@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\Report;

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">

    @if( $auth->can( 'create', Schedule::class )) 
        <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.create'   ) }}">
            <span>新規スケジュール</span>
        </a>
    @else
        @php
            $route = route( 'groupware.calendar.index' );
            $title = "予定作成権限のあるカレンダーがありません。まずはカレンダーを作成してください";
        @endphp
        <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">カレンダー作成</a>
    @endif
    
    @if( $auth->can( 'create', Task::class )) 
        <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.task.create'   ) }}">
            <span>新規タスク</span>
        </a>
    @else
        @php
            $route = route( 'groupware.tasklist.index' );
            $title = "タスク作成権限のあるタスクリストがありません。まずはタスクリストを作成してください";
        @endphp
        <a class="btn btn-warning border-dark col-2 col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">タスクリスト作成</a>
    @endif
    
    
        
</div>
