@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$calprop = CalProp::where( 'calendar_id', $calendar->id )->where( 'user_id', user_id() )->first();

$route_update_calendar = route( 'groupware.calendar.update', [ 'calendar' => $calendar ] );
$route_update_calprop  = route( 'groupware.calprop.update',  [ 'calprop'  => $calprop  ] );
$route_create_schedule = route( 'groupware.schedule.create', [ 'calendar_id' => $calendar->id ] );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.calendar.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                        @can( 'update', $calendar )
                                <a class="btn btn-warning text-dark" href="{{ $route_update_calendar }}">カレンダー管理者設定変更</a>
                        @endcan
                        @if( $calendar->canRead( user_id() ))
                            <a class="btn btn-outline-secondary text-dark" href="{{ $route_update_calprop }}">表示設定・Googleカレンダー同期設定</a>
                        @endif                            

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <hr>
                        @include( 'groupware.calendar.show_parts' )
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
