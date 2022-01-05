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
                        <div class="row no-gutters">
                            @if( auth( 'admin' )->check() )
                                <a class="btn btn-warning col-3 m-1" href="{{ $route_edit   }}">変更</a> 
                                <a class="btn btn-danger  col-3 m-1" href="{{ $route_delete }}">削除</a>
                            @endif
                        
                            <div class="col-12 col-md-4 my_label">{{ config( 'user.columns_name' )['name'] }}</div>
                            <div class="col-12 col-md-6">{{ $user->name }}</div>
        
                            <div class="col-12 col-md-4 my_label">{{ config( 'user.columns_name' )['email'] }}</div>
                            <div class="col-12 col-md-6">{{ $user->email }}</div>
                            
                            <div class="col-12 col-md-4 my_label">部署名</div>
                            <div class="col-12 col-md-6">{{ optional( $user->dept )->name }}</div>
                            
                            <div class="col-12 col-md-4 my_label">役職</div>
                            <div class="col-12 col-md-6">{{ $user->grade }}</div>
                            
                            @if( $user->retired ) 
                                <div class="col-12 col-md-4 my_label">退社</div>
                                <div class="col-12 col-md-6">退社済み</div>
                                <div class="col-12 col-md-4 my_label">{{ config( 'user.columns_name' )['date_of_retired'] }}</div>
                                <div class="col-12 col-md-6">{{ $user->date_of_retired }}</div>
                            @endif
        
                            <div class="col-12 col-md-6 offset-md-4 m-2">
                                    {{ BackButton::form() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
