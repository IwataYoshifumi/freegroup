@extends('layouts.app')

@php

use Illuminate\Support\Facades\Route;
use App\Models\Vacation\Dept;
use App\Models\Vacation\User;

@endphp

@section('content')
<div class="container col-12 col-md-10">
        <div class="card">
            <div class="card-header">パスワード変更</div>
            
            @include( 'layouts.error' )
            @include( 'layouts.flash_message' )
            
            @if( Route::currentRouteName() == 'vacation.user.password' )
                <div class="alert alert-warning h5 flush">
                パス―ワードは8文字以上で設定ください。<br>
                アルファベット、数字、記号が使えます。
                </div>
            @endif

            <div class="card-body">
                <div class="container border border-round border-dark p-0">
                    <div class="d-none d-md-block  bg-light container">
                    <div class="row text-center p-1">
                        <div class="col-2 m-1 align-middle font-weight-bold">社員番号</div>
                        <div class="col-3 m-1 align-middle font-weight-bold">名前</div>
                        <div class="col-3 m-1 align-middle font-weight-bold">部署名</div>
                        <div class="col-2 m-1 align-middle font-weight-bold">役職</div>
                    </div>
                    </div>
                    <div class="row p-1">
                        <div class="col-3 d-md-none font-weight-bold">社員番号</div>
                        <div class="col-7 col-md-2 m-1 align-middle">{{ $user->code }}</div>
                        <div class="col-3 d-md-none font-weight-bold">名前</div>
                        <div class="col-7 col-md-3 m-1 align-middle">{{ $user->name }}</div>
                        <div class="col-3 d-md-none font-weight-bold">部署名</div>
                        <div class="col-7 col-md-3 m-1 align-middle">{{ $user->department->name }}</div>
                        <div class="col-3 d-md-none font-weight-bold">役職</div>
                        <div class="col-7 col-md-2 m-1 align-middle">{{ $user->grade }}</div>
                    </div>
                </div>

                <BR>
                <form method="POST" action="{{ route('vacation.user.password.update', [ 'user' => $user->id, 'class' => 'container' ] ) }}">
                    @csrf
                    <div class="container">
                        <div class='row w-100 m-2'>                  
                            <label for="password" class="col-12 col-md-4 form-label text-md-right">パスワード</label>
                            <input id="password" type="password" class="col-12 col-md-8 form-control" 
                                   name="password" required autocomplete="new-password">
                        </div>
                        <div class="row w-100 m-2">
                            <label for="password-confirm" class="col-12 col-md-4 text-md-right">パスワード（確認）</label>
                            <input id="password-confirm" type="password" class="col-12 col-md-8 form-control" 
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="container clearfix p-2">            
                        <button type="submit" class="btn btn-success col-5 col-md-4 float-right">パスワード変更</button>
                    </div>
                </form>
            </div>
        </div>
</div>
@endsection
