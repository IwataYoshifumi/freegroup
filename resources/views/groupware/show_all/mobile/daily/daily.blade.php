@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\TaskList;
use App\Http\Helpers\BackButton;

#dd( $request->all(), $returns );

$route_name = Route::currentRouteName();

@endphp

@extends('layouts.app_dialog')
@section('content')

<div style="width: 100%;" class="shadow">
    @include( 'groupware.show_all.mobile.daily.daily_button' )
</div>

<div style="width: 100%;" class="">
    @include( 'groupware.show_all.mobile.daily.daily_body' )
</div>
                
@endsection
