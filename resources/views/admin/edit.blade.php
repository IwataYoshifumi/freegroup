@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Admin;

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'admin.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <form method="POST" action="{{ route('admin.update', [ 'admin' => $admin ] ) }}">
                        @csrf
                        {{ Form::hidden( 'id', $admin->id ) }}

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['name'] }}</label>

                            <div class="col-md-6">
                                <input type="text" name="name" value="{{ old( 'name', $admin->name ) }}" required autofocus class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['email'] }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email', $admin->email ) }}" class="form-control" autocomplete="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['password'] }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['password'] }}（確認）</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['retired'] }}</label>
                            <div class="col-md-6">

                                {{ Form::select( 'retired', [ 0 => '在職', 1 => '退社' ],  
                                                old( 'retired', $admin->retired ), [ 'class' => 'form-control', 'id' => 'retired' ]   ) }}
                                {{ Form::date( 'date_of_retired', old( 'date_of_retired', $admin->date_of_retired ), 
                                               [ 'class' => 'form-control', 'id' => 'date_of_retired' ] ) }} 

                            </div>

                             
                        </div>
                        

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-warning">変更実行</button>
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
