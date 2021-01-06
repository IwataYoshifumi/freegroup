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

                    <table class="table table-striped table-sm">
                        @foreach( $schedules as $schedule )
                            @php
                                $href = route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] );                            
                            @endphp
                            <tr>
                                <td><a class='btn btn-sm btn-outline-secondary' href="{{ $href }}">詳細</a></td>
                                <td>{{ $schedule->name }}</td>
                                <td>{{ $schedule->place }}</td>
                                <td>{{ $schedule->print_time() }}</td>
                                <td>{{ $schedule->memo }}</td>
                            </tr>
                            @if( $loop->last )
                                <tr><th colspan=6 class="bg-white m-1 mt-3 p-1">
                                    {{ $schedules->links() }}
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

