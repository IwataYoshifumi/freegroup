@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Report;

if( ! isset( $customer ) ) { $customer = null; }

$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

$users     = $task->users;
$creator   = $task->creator;
$updator   = $task->updator;
$tasklist  = $task->tasklist;

$files     = $task->files;
$customers = $task->customers;
// $schedules = $task->schedules;

# dd( $user, $files );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.task.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'groupware.task.show_button' )
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'task' => optional($task)->id ] ) ]) }}
                        @method( 'GET' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $task )->id ) }}
                        
                        @include( 'groupware.task.show_parts' ) 
                        
                        <div class="col-12">
                            {{ BackButton::form() }}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $( function() { $('.uitooltip').uitooltip(); });
</script>


@endsection

