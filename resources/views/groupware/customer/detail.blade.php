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

$request = new Request;
$today = new Carbon( 'today' );

//　スケジュールを検索
//  直近１年前からの予定を検索
//
$request->start_date = $today->copy()->subYear()->format('Y-m-d');

$request->show_hidden_calendars = 1;
$request->calendar_permission = "reader";
$request->customers = [ $customer->id ];
$request->calendars = Calendar::getCanRead( user_id() )->pluck('id')->toArray();
$request->order_by = [ 'time' ];
$request->asc_desc = [ 'desc' ];

$request->pagination = 30;
$returns = SearchForShowALLIndex::search( $request );
$schedules = $returns['schedules'];

if_debug( $schedules, $customer->schedules );

//　タスクの検索
//  直近１年のタスクを検索（未完・完了とも）
//
$request = new Request;
$request->start_date = $today->copy()->subYear()->format('Y-m-d');
$request->show_hidden_tasklists = 1;
$request->tasklist_permission = "reader";
$request->task_status = '';
$request->tasklists = TaskList::getCanRead( user_id() )->pluck('id')->toArray();
$request->customers = [ $customer->id ];
$request->order_by = [ 'time' ];
$request->asc_desc = [ 'desc' ];
$request->pagination = 30;

$returns = SearchForShowALLIndex::search( $request );
$tasks  = $returns['tasks'];

//　直近の日報
//
$request->end_date = $today->format('Y-m-d');

$request->show_hidden_report_lists = 1;
$request->report_list_permission = "reader";
$request->customers = [ $customer->id ];
$request->report_lists = ReportList::getCanRead( user_id() )->pluck('id')->toArray();
$request->order_by = [ 'time' ];
$request->pagination = 100;
$returns = SearchForShowALLIndex::search( $request );
$reports = $returns['reports'];

#$reports   = $customer->reports()->orderBy( 'start_date' )->get();



@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">{{ config( Route::currentRouteName() ) }}</div>
                        <a   class="btn col-1 ml-auto" href="{{ route( 'customer.edit' ,   [ 'customer' => $customer->id ] ) }}" title="変更">@icon( edit )</a>
                        <a   class="btn col-1 " href="{{ route( 'customer.delete' , [ 'customer' => $customer->id ] ) }}" title="削除">@icon( trash )</a>
                    </div>
                </div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'groupware.customer.detail_parts' )

                </div>
            </div>

            <!--
              --
              -- スケジュール
              --
              -->
            @include( 'groupware.customer.detail_schedules' )
            <!--
              --
              -- タスク
              --
              -->
            @include( 'groupware.customer.detail_tasks'     )
            <!--
              --
              -- 日報
              --
              -->
            @include( 'groupware.customer.detail_reports' )

            <div class="card-body">
                {{ BackButton::form() }}
            </div>


        </div>
    </div>
</div>

<!--
  --
  -- 詳細表示ダイヤログ
  --
  -->
@include( 'groupware.show_all.dialog.show_detail' )

@endsection
