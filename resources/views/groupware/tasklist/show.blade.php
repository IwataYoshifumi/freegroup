@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$user = auth( 'user' )->user();
$taskprop = TaskProp::where( 'task_list_id', $tasklist->id )->where( 'user_id', $user->id )->first();

$route_update_tasklist = route( 'groupware.tasklist.update', [ 'tasklist' => $tasklist ] );
$route_delete_tasklist = route( 'groupware.tasklist.delete', [ 'tasklist' => $tasklist ] );
$route_show_taskprop   = route( 'groupware.taskprop.show',  [ 'taskprop'  => $taskprop  ] );
$route_create_task = route( 'groupware.task.create', [ 'tasklist_id' => $tasklist->id ] );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.tasklist.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                        @can( 'update', $tasklist )
                            <a class="btn uitooltip icon_btn" href="{{ $route_update_tasklist }}" title="タスクリスト管理者設定　変更">
                                <i class="fas fa-pen"></i>
                            </a>
                        @endcan
                        @if( $tasklist->canRead( user_id() ))
                            <a class="btn uitooltip icon_btn" href="{{ $route_show_taskprop }}" title="【個人設定】色・表示設定">
                                <i class="fas fa-cog"></i>
                            </a>
                        @endif
                        @if( $user->can( 'delete', $tasklist ))
                            <a class="btn uitooltip icon_btn" href="{{ $route_delete_tasklist }}" title="タスクリスト削除">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        @endif

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.tasklist.show_parts' )
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
