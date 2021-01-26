@php
use App\myHttp\Schedule\Models\Schedule;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

$route_name = Route::currentRouteName();

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @include( 'groupware.schedule2.find_form' )

                    <table class="table table-striped table-sm">
                        @foreach( $schedules as $schedule )
                            @if( $loop->first )
                                <tr>
                                    <th>&nbsp:</th>
                                    <th>作成者（関連社員）</th>
                                    <th>件名</th>
                                    <th>場所</th>
                                    <th>日時</th>
                                </tr>
                            @endif
                        
                            @php
                                if( $request->search_condition == "only_creator" and is_null( $schedule->user )) { continue; } 
                            
                                $href = route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] );                            
                            @endphp
                            

                            <tr>
                                <td><a class='btn btn-sm btn-outline-secondary' href="{{ $href }}">詳細</a></td>
                                <td>{{ op( $schedule->creator )->name }}
                                    @if( count( $schedule->users )) 
                                        @php 
                                            $title = "関連社員：" . count( $schedule->users ) . "名"; 
                                        @endphp
                                        <span class="uitooltip" title="{{ $title }}">@icon( user-friends )</span>
                                    @endif
                                </td>
                                <td>{{ $schedule->name }}</td>
                                <td>{{ $schedule->place }}</td>
                                <td>{{ $schedule->p_dateTime() }}</td>
                            </tr>
                            @if( $loop->last )
                                <tr><th colspan=6 class="bg-white m-1 mt-3 p-1">
                                    @if( method_exists( $schedules, 'links' ))
                                        {{ $schedules->links() }}
                                    @endif
                                </th></tr>
                            @endif
                            
                        @endforeach
                    </table>
                    <div class="w-100"></div>
                    @php
                    @endphp
        
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

