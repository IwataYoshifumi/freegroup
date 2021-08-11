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
$taskprop = TaskProp::where( 'task_list_id', $facility->id )->where( 'user_id', $user->id )->first();

$route_update_facility = route( 'groupware.facility.update', [ 'facility' => $facility ] );
$route_delete_facility = route( 'groupware.facility.delete', [ 'facility' => $facility ] );
$route_show_taskprop   = route( 'groupware.taskprop.show',  [ 'taskprop'  => $taskprop  ] );
$route_create_task = route( 'groupware.task.create', [ 'facility_id' => $facility->id ] );



@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.facility.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                        @can( 'update', $facility )
                            <a class="btn uitooltip icon_btn" href="{{ $route_update_facility }}" title="設備管理者設定　変更">
                                <i class="fas fa-pen"></i>
                            </a>
                        @endcan

                        @can( 'delete', $facility )
                            <a class="btn uitooltip icon_btn" href="{{ $route_delete_facility }}" title="設備削除">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        @endcan

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.facility.show_parts' )
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
