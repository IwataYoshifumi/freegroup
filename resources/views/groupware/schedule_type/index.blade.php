@php
use App\myHttp\Schedule\Models\ScheduleType;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\BackButton;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule_type.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @if( count( $schedule_types )) 
                        <table class="table table-border">
                            <tr class="bg-light">
                                <th>変更</th>
                                <th>スケジュールタイプ名</th>
                                <th>Google カレンダーID</th>
                            </tr>
                            @foreach( $schedule_types as $type )    
                                <tr>
                                    <td><a class="btn btn-sm btn-warning" href="{{ route( 'groupware.schedule.type.edit', [ 'schedule_type' => $type->id ] ) }}">変更</a></td>
                                    <td>
                                        <span class="m-2 p-2" style="background-color: {{ $type->color }}; color: {{ $type->text_color }};">
                                            {{ $type->name }}
                                        </span>
                                    </td>
                                    <td>{{ $type->google_calendar_id }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <div>
                            {{ BackButton::form() }}
                        </div>
                        
                    @endif
                    <div class="w-100"></div>
        
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp



@endsection