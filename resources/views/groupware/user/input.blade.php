@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

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
                    
                    <form method="POST" action="{{ url()->full() }}">
                        @csrf
                        {{ Form::hidden( 'id', $user->id ) }}

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>

                            <div class="col-md-6">
                                <input type="text" name="name" value="{{ old( 'name', $user->name ) }}" required autofocus class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email ) }}" class="form-control" autocomplete="email">
                            </div>
                        </div>
                    
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">部署名</label>

                            <div class="col-md-6">
                                @php
                                    $depts_list = Dept::getArrayForSelect();
                                @endphp
                                {{ Form::select( 'dept_id', $depts_list, old( 'dept_id', $user['dept_id'] ), ['class' => 'form-control required' ] ) }}
                                
                                @error('dept_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">役職</label>

                            <div class="col-md-6">
                                <input id="grade" type="text" class="form-control @error('grade') is-invalid @enderror" 
                                        name="grade" value="{{ old('grade', $user->grade) }}">

                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['password'] }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['password'] }}（確認）</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        
                        @if( Route::currentRouteName() == "groupware.user.edit" )
                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['retired'] }}</label>
                                <div class="col-md-6">
    
                                    {{ Form::select( 'retired', [ 0 => '在職', 1 => '退社' ],  
                                                    old( 'retired', $user->retired ), [ 'class' => 'form-control', 'id' => 'retired' ]   ) }}
                                    {{ Form::date( 'date_of_retired', old( 'date_of_retired', $user->date_of_retired ), 
                                                   [ 'class' => 'form-control', 'id' => 'date_of_retired' ] ) }} 
    
                                </div>
                            </div>
                        @endif

                        <div class="form-group row mb-0">
                            @php
                                if( Route::currentRouteName() == "groupware.user.create" ) {
                                    $submit_button = "新規　社員登録";
                                    $class = "btn-primary";
                                } else {
                                    $submit_button = "社員情報　修正";
                                    $class = "btn-warning";
                                }
                            @endphp
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn {{ $class }}">{{ $submit_button }}</button>
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
