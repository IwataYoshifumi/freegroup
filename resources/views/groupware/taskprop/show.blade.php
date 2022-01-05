@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$user = auth( 'user' )->user();
$tasklist = $taskprop->tasklist;

$route_show_tasklist   = route( 'groupware.tasklist.show', [ 'tasklist' => $tasklist ] );
$route_update_tasklist = route( 'groupware.tasklist.update', [ 'tasklist' => $tasklist ] );
$route_update_taskprop  = route( 'groupware.taskprop.update',  [ 'taskprop'  => $taskprop  ] );

$info = "<i class='fas fa-minus-circle' style='color:lightgray'></i>";
$permissions = Task::getPermissions();

if_debug( $taskprop, $taskprop->user->id, $user->id, $user->can( 'update', $taskprop ));

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.tasklist.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                        @can( 'update', $taskprop )
                            <a class="btn icon_btn" href="{{ $route_update_taskprop }}" title="個人設定変更"> @icon( edit ) </a>
                        @endcan
                        
                        @can( 'view', $tasklist )
                                <a class="btn icon_btn" href="{{ $route_show_tasklist }}" title="タスクリスト管理情報"> @icon( config ) </a>
                        @endcan
                        
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <div class="form-group row no-gutters">
                            <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">タスクリスト名{!! $info !!}   {{-- htmlspecialchars OK --}}
                            </label>
                            <div class="col-12 col-md-6 m-1">
                                {{ $tasklist->name }}
                            </div>
                            
                            <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">タスクリスト表示名・色設定</label>
                            <div class="col-12 col-md-6 m-1" style='{{ $taskprop->style() }}'>
                                {{ $taskprop->name }}
                            </div>
                        
                            <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">タスクリスト公開種別{!! $info !!}</label>   {{-- htmlspecialchars OK --}}
                            <div class="col-12 col-md-6 m-1">
                                {{ TaskList::getTypes()[$tasklist->type] }}
                            </div>
                        
                            @if( $tasklist->not_use )
                                <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">【管理者設定】</label>
                                <div class="col-12 col-md-6 m-1"><span class="alert-danger p-2 text-dark">新規タスク　追加不可</span></div>
                            @endif

                            @if( $tasklist->disabled )
                                <label for="dept_id" class=col-12 "col-md-4 my_label text-md-right m-1">【管理者設定】</label>
                                <div class="col-12 col-md-6 m-1"><span class="alert-danger p-2 text-dark">既存タスク　修正不可</span></div>
                            @endif
                            
                            <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">【個人設定】作成タスク変更権限（初期値）</label>
                            <div class="col-12 col-md-6 m-1">{{ $permissions[ $taskprop->default_permission ] }}</div>

                            @if( $taskprop->not_use and ! $tasklist->not_use )
                                <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">【個人設定】</label>
                                <div class="col-12 col-md-6 m-1"><span class="alert-warning p-2 text-dark">新規にタスクを作成しまし</span></div>
                            @endif

                            @if( $taskprop->hide )
                                <label for="dept_id" class="col-12 col-md-4 my_label text-md-right m-1">【個人設定】</label>
                                <div class="col-12 col-md-6 m-1"><span class="alert-warning p-2 text-dark">タスクを表示しない</span></div>
                            @endif

                            <div class="col-4 m-1"></div>
                            <ul class="col-12 m-1">
                                <ui>{!! $info !!}はタスクリスト管理者設定</ui>   {{-- htmlspecialchars OK --}}
                            </ul>
                        </div>
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
