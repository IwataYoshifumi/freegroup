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

$user = auth( 'user' )->user();
$calprop = CalProp::where( 'calendar_id', $calendar->id )->where( 'user_id', $user->id )->first();

$route_update_calendar = route( 'groupware.calendar.update', [ 'calendar' => $calendar ] );
$route_delete_calendar = route( 'groupware.calendar.delete', [ 'calendar' => $calendar ] );
$route_show_calprop  = route( 'groupware.calprop.show',  [ 'calprop'  => $calprop  ] );
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
                            <a class="btn uitooltip icon_btn" href="{{ $route_update_calendar }}" title="カレンダー管理者設定　変更">
                                <i class="fas fa-pen"></i>
                            </a>
                        @endcan
                        @if( $calendar->canRead( user_id() ))
                            <a class="btn uitooltip icon_btn" href="{{ $route_show_calprop }}" title="【個人設定】色・Googleカレンダー同期等">
                                <i class="fas fa-cog"></i>
                            </a>
                        @endif
                        @if( $user->can( 'delete', $calendar ))
                            <a class="btn uitooltip icon_btn" href="{{ $route_delete_calendar }}" title="カレンダー削除">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        @endif

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.calendar.show_parts' )
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
