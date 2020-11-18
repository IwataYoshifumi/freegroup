@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\BackButton;

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'user.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                
                @include( 'user.show_button' )

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <form method="POST" action="{{ route('user.store') }}">
                        @csrf

                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['name'] }}</label>
                            <div class="col-md-6">{{ $user->name }}</div>
                        </div>

                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['email'] }}</label>
                            <div class="col-md-6">{{ $user->email }}</div>
                        </div>
                        
                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">部署名</label>
                            <div class="col-md-6">{{ optional( $user->dept )->name }}</div>
                        </div>
                        
                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">役職</label>
                            <div class="col-md-6">{{ $user->grade }}</div>
                        </div>
                        
                        @if( $user->retired ) 
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">退社</label>
                                <div class="col-md-6">退社済み</div>
                            </div>
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['date_of_retired'] }}</label>
                                <div class="col-md-6">{{ $user->date_of_retired }}</div>
                            </div>
                        @endif


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                {{ BackButton::form() }}

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
