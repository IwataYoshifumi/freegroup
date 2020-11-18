@extends('layouts.app')

@php

use App\Models\Vacation\Dept;
use App\Models\Vacation\User;

use App\Http\Helpers\BackButton;

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @include( 'vacation.users.menu' )

            <div class="card">
                <div class="card-header">従業員　新規登録</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <form method="POST" action="{{ route('vacation.user.store') }}">
                        @csrf

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
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">職級</label>

                            <div class="col-md-6">
                                <input id="grade" type="text" class="form-control @error('grade') is-invalid @enderror" 
                                        name="grade" value="{{ old('grade') }}">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-md-right">社員番号</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') }}" required autocomplete="name" autofocus>

                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">中途・新卒</label>

                            <div class="col-md-6">
                                @php
                                    $select_carrier = ['' => '', '新卒'=>'新卒', '中途' => '中途' ];
                                @endphp
                                
                                {{ Form::select( 'carrier', $select_carrier , old( 'carrier'  ), ['class' => 'form-control'] ) }}
                                
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">入社年月日</label>

                            <div class="col-md-6">
                                {{ Form::date( 'join_date', old( 'join_date' ), ['class' => 'form-control'] ) }}
                                
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">

                            </div>
                        </div>
                        


                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">パスワード</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">パスワード（確認）</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">閲覧権限</label>

                            <div class="col-md-6">

                                {{ Form::select( 'browsing', config( 'vacation.constant.authority.browsing' ) , old( 'browsing'  ), ['class' => 'form-control'] ) }}
                                
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">備考</label>

                            <div class="col-md-6">
                                {{ Form::textarea( 'memo', old( 'memo' ), [ 'class' => 'form-control' ] ) }}
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-success">登録</button>

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
