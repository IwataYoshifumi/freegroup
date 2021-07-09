@extends('layouts.app')

@php

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Http\Helpers\BackButton;


use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Controllers\Search\SearchForShowALLIndex;

use App\Http\Helpers\ScreenSize;


$request = new Request;
$today = new Carbon( 'today' );

//　スケジュールを検索
//
$request->start_date = $today->format('Y-m-d');
$request->end_date   = $today->format('Y-m-d');

$request->show_hidden_calendars = 0;
$request->calendar_permission = "writer";
$request->users = [ user_id() ];
$request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
$request->order_by = [ 'time' ];
$request->pagination = 100;
$returns = SearchForShowALLIndex::search( $request );
$schedules = $returns['schedules'];

if_debug( $returns );

//　未完タスクの検索
//
$request = new Request;
$request->show_hidden_tasklists = 0;
$request->tasklist_permission = "writer";
$request->task_status = '未完';
$request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();
$request->users = [ user_id() ];
$request->order_by = [ 'time' ];
$request->pagination = 100;

$returns = SearchForShowALLIndex::search( $request );
$tasks  = $returns['tasks'];

//　
//
$reports   = $user->reports->load( 'user' );


if_debug( $tasks, $returns );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'layouts.flash_message' )
            @include( 'layouts.error' )

            @include( 'groupware.user.detail_parts' )
            
            <div class="card-body text-center">
                <div class="h2 d-none d-lg-block">
                   今日の日付： {{ $today->format( 'Y年n月j日'  ) }} 【{{ p_date_jp( $today->format('w') ) }}】
                </div>
            </div>
            
            @include( 'groupware.user.detail_schedules' )
            @include( 'groupware.user.detail_tasks' )
            
            
            {{--
            @include( 'groupware.user.detail_reports' )
            --}}


        </div>
    </div>
</div>

@include( 'groupware.modal_window.include_detail_objects' )

{{ ScreenSize::rendarScriptToGetScreenSize() }}

@endsection
