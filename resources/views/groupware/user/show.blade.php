@extends('layouts.app')

@php

use App\myHttp\GroupWare\Models\User;
use App\Http\Helpers\BackButton;


$route_edit   = route( 'groupware.user.edit',   [ 'user' => $user->id ] );
$route_delete = route( 'groupware.user.delete', [ 'user' => $user->id ] );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.user.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                
                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    <div class="m-1 w-100 container">
                        @if( auth( 'admin' )->check() )
                            <a class="btn btn-warning col-3 m-1" href="{{ $route_edit   }}">変更</a> 
                            <a class="btn btn-danger  col-3 m-1" href="{{ $route_delete }}">削除</a>
                        @endif
                    </div>
                    
                    
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
