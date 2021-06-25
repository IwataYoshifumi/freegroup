@php

use App\myHttp\GroupWare\Models\Task;

$options = [ 'from_menu' => 1 ];
$auth = auth( 'user' )->user();

#$route_to_search_tasks = route( 'groupware.task.index', $options  ); 
$route_to_search_tasks = route( 'groupware.show_all.index', [ 'writable_tasklist' => 1, 'set_defaults' => 1 ] );

@endphp

<div class="row m-1 w-100 container">
    
    @if( $auth->can( 'create', Task::class ))
        <a class="btn btn-primary col col-lg-3 m-1" href="{{ route( 'groupware.task.create'   ) }}">
            <div class="d-block d-lg-none">新規タスク</div>
            <div class="d-none d-lg-block">新規タスク作成</div>
        </a>
    @else
        @php
            $route = route( 'groupware.tasklist.index' );
            $title = "タスク作成権限のあるタスクリストがありません。タスクリストを作成するか、タスクリスト管理者に権限割当を依頼してください";
        @endphp
        <a class="btn btn-warning border-dark col-3 col-lg-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">タスクリスト作成</a>
        
        
    @endif
    

    <a class="btn btn-menu col col-lg-3 m-1" href="{{ $route_to_search_tasks }}">
        <div class="d-block d-lg-none">検索</div>
        <div class="d-none d-lg-block">タスク検索</div>
    </a>
    
    <a class="btn btn-menu col-lg-3 m-1 d-none d-lg-block" href="{{ route( 'groupware.tasklist.index', $options  ) }}">
        <div>タスクリスト</div>
    </a>
        
</div>
