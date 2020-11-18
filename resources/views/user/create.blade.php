@extends('layouts.app')

@php

use App\Models\User;
use App\Models\Dept;

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'user.menu_button' )

            <div class="card">
                <div class="card-header">ユーザ登録</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    {{ Form::open( [ 'method' => 'POST', 'route' => 'user.store' ] ) }}
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>

                            <div class="col-md-6">
                                <input type="text" name="name" value="{{ old( 'name' ) }}" required autofocus class="form-control">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" autocomplete="email">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">部署名</label>

                            <div class="col-md-6">
                                @php
                                    $depts_list = Dept::getArrayForSelect();
                                @endphp
                                {{ Form::select( 'dept_id', $depts_list, old( 'dept_id' ), ['class' => 'form-control required' ] ) }}
                                
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
                                        name="grade" value="{{ old('grade') }}">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">パスワード</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">パスワード（確認）</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">登録</button>

                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
