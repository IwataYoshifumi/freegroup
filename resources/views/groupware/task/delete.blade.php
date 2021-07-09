@extends('layouts.app')

@php

if( ! isset( $customer ) ) { $customer = null; }

use App\Http\Helpers\BackButton;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Task;

use App\myHttp\GroupWare\Requests\SubRequests\ComfirmDeletionRequest;

if( ! isset( $customer ) ) { $customer = null; }

$customers = $task->customers;
$users     = $task->users;
$user      = $task->user;
$files     = $task->files;
$tasklist  = $task->tasklist;

$route_name = Route::currentRouteName();

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.task.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}( task_id {{ $task->id }} )</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @if( $route_name == "groupware.task.delete" and ! count( $errors ))
                        <div class="alert alert-danger">タスクを削除します。よろしいですか。</div>
                    @elseif( $route_name == "groupware.task.deleted" ) 
                        <div class="alert alert-warning">タスクを削除しました。</div>
                    @endif
                    
                    
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'task' => optional($task)->id,  ]), 'name' => 'delete_form' ] ) }}
                        @method( 'DELETE' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $task )->id ) }}
                        
                        @include( 'groupware.task.show_parts' )
                        
                        
                        <div class="col-12">

                            @if( $route_name == 'groupware.task.delete'  )
                                <div>
                                    <label for="comfirm_deletion">関連データも全て削除されます。この操作は取り消しできません。</label>
                                    <input type="checkbox" name="{{ ComfirmDeletionRequest::getInputName() }}" value=1 class="checkboxradio" id="comfirm_deletion">
                                </div>
                            
                                <a class="btn btn-danger text-white" onClick="document.delete_form.submit()">削除実行</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
