@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Route;
    use App\Models\Vacation\Application;
    use App\Models\Vacation\User;
    use App\Models\Vacation\Dept;
    
    use App\Http\Helpers\BackButton;

    $user      = $application->user;
    $approvals = $application->approvals;

@endphp

@section('content')
<div class="container">
    <div class="col-12 w-lg-80">
        <div class="card clearfix">
            <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
            @include( 'layouts.error' )
            @include( 'layouts.flash_message' )
            @include( 'vacation.application.parts_show' )

            <div class='container'>
                <div class="row w-80 clearfix">
                    @if( $user->id == Auth::user()->id )
                        {{ Form::open( ['url'    => url()->route( 'vacation.application.dropped', [ 'application'=> $application ] ), 
                                        'method' => 'post' ] ) }}
                        <button type='submit' class='btn btn-danger float-left w-45 w-lg-30 m-3'>申請 取り下げ実行</button>
                        {{ Form::close() }}
                    @endif
                    {{ BackButton::form() }}
                        
                    <!--<a class='btn btn-outline-secondary float-right col-3 m-2' href='{{ url()->previous() }}'>戻る</a>-->
                </div>
            </div>
        </div>
    </div>
</div>

@php

@endphp 

@endsection
