@extends('layouts.app')

@php

use Illuminate\Support\Facades\Route;
use App\Models\Dept;
use App\Models\User;

//　パスワードのバリデーション
//
$m = config( 'password.error.'.config('user.password_valicator'));
//dump( $m );
@endphp

@section('content')
<div class="container col-12 col-md-10" style='max-width:560px'>
        <div class="card">
            <div class="card-header">パスワード変更</div>
            
            @include( 'layouts.error' )
            @include( 'layouts.flash_message' )
            
            @if( ! is_null( $m ))
                <div class="alert alert-warning h5 flush m-2">
                    {{ $m[array_key_first( $m )] }}
                </div>
            @endif

            <div class="card-body">

                <form method="POST" action="{{ route('user.change_password', [ 'class' => 'container' ] ) }}">
                    @csrf
                    <div class="container">
                        <div class='row w-100 m-1'>                  
                            <label for="password" class="col-12 col-md-4 form-label text-md-right">パスワード</label>
                            <input id="password" type="password" class="col-12 col-md-8 form-control" 
                                   name="password" required autocomplete="new-password">
                        </div>
                        <div class="row w-100 m-1">
                            <label for="password-confirm" class="col-12 col-md-4 text-md-right">確認</label>
                            <input id="password-confirm" type="password" class="col-12 col-md-8 form-control" 
                                   name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="container clearfix p-2">            
                        <button type="submit" class="btn btn-success col-5 col-md-4 float-right">パスワード変更</button>
                    </div>
                    <script type="text/javascript">
                        document.getElementById('password').focus();
                        
                    </script>
                </form>
            </div>
        </div>
</div>
@endsection
