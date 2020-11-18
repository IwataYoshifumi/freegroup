@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

@endphp

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'test.google.calendar.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @if( count( $events )) 
                        <table class="table table-border">
                            @foreach( $events as $event ) 
                                <tr>
                                    <td><a class='btn btn-sm btn-warning' href='{{ route( 'calendar.edit', [ 'gid'=> $event->id ] ) }}'>変更</a></td>
                                    <td>{{ $event->id }}</td>
                                    <td>{{ $event->summary }}</td>
                                    <td>{{ $event->start->date }} {{ $event->start->dateTime }}</td>
                                    <td>{{ $event->end->date   }} {{ $event->end->dateTime   }}</td>
                                    
                                    
                                </tr>
    
                            @endforeach
                        </table>
                    @endif
                    <div class="w-100"></div>


                </div>
            </div>
        </div>
    </div>
</div>



@endsection