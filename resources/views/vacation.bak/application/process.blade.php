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
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
            <div class='row'>
                <div class='col-12'>
                    @if (Session::has('flash_message'))
                         <div class="alert alert-primary">{!! Session::get('flash_message') !!}</div>
                    @endif
                        
                </div>
            </div>

            @include( 'vacation.application.parts_show' )

            <div class='container'>
                <div class="row">
                    @if( $user->id == Auth::user()->id )
                        {{ Form::open( ['url'    => url()->route('vacation.application.processed', [ 'application' => $application ] ), 
                                        'method' => 'post', 'class' => 'w-100 bg-dark', 'id' => 'form-processed' ] ) }}
                        {{ Form::close() }}       
                        
                        <button id='submit_button' type='button' class='btn btn-primary m-3 col-4'>休暇取得　完了</button>
                        <script>
                            $('#submit_button').click( function() {
                               $('#form-processed').submit(); 
                            });
                        </script>
                    @endif
                    {{ BackButton::form( 'col-3 m-3' ) }}
                </div>    
            </div>
        </div>
    </div>
</div>
@php

@endphp 

@endsection

