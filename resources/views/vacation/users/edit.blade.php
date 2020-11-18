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
                <div class="card-header">従業員　変更( User ID {{ $user->id }} )</div>
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )


                <div class="card-body">
                    <form method="POST" action="{{ route('vacation.user.update', [ 'user' => $user->id ] ) }}">
                        @csrf
                        {{ Form::hidden( 'id', $user->id ) }}
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
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">職級</label>

                            <div class="col-md-6">
                                <input id="grade" type="text" class="form-control @error('grade') is-invalid @enderror" 
                                        name="grade" value="{{ old('grade', $user['grade'] ) }}">

                                @error('grade')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">社員番号</label>

                            <div class="col-md-6">
                                <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code', $user['code'] ) }}" required autocomplete="name" autofocus>

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $user['name'] ) }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">中途・新卒</label>

                            <div class="col-md-6">
                                @php
                                    $select_carrier = ['' => '', '新卒'=>'新卒', '中途' => '中途' ];
                                @endphp
                                
                                {{ Form::select( 'carrier', $select_carrier , old( 'carrier', $user['carrier'] ), ['class' => 'form-control'] ) }}
                                
                                @error('carrier')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">入社年月日</label>

                            <div class="col-md-6">
                                {{ Form::date( 'join_date', old( 'join_date', $user['join_date'] ), ['class' => 'form-control'] ) }}
                                
                                @error('join_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                       name="email" value="{{ old('email', $user['email'] ) }}" autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">閲覧権限</label>

                            <div class="col-md-6">

                                {{ Form::select( 'browsing', config( 'vacation.constant.authority.browsing' ) , old( 'browsing', $user->browsing  ), ['class' => 'form-control'] ) }}
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="retire" class="col-md-4 col-form-label text-md-right">退社</label>
                            <div class="col-md-6">
                                
                                @php
                                $err_class = "";
                                if( $errors->has( 'retired_date' )) { $err_class = 'is-invalid'; } 
                                @endphp
                                
                                

                                {{ Form::select( 'retired', [ 0 => '-', 1 => '退社' ],  
                                                old( 'retired', $user['retired'] ), [ 'class' => 'form-control', 'id' => 'retired' ]   ) }}
                                {{ Form::date( 'date_of_retired', old( 'date_of_retired', $user['date_of_retired'] ), 
                                               [ 'class' => 'form-control '.$err_class, 'id' => 'retired_date' ] ) }} 
                                @error( 'retired_date' )
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                               
                                               
                            </div>
                            <script>
                                $('#retired').change( function() {
                                    if( $(this).val() == 1 ) {
                                        $('#retired_date').css('visibility', 'visible');
                                    } else {
                                        $('#retired_date').css('visibility', 'hidden');
                                        $('#retired_date').val( '' );
                                    }
                                });
                                $(document).ready(function(){
                                    $('#retired').change();
                                });

                                
                                
                            </script>
                        </div>
                        
                        
                        
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">備考</label>

                            <div class="col-md-6">
                                {{ Form::textarea( 'memo', old( 'memo', $user['memo'] ), [ 'class' => 'form-control' ] ) }}
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-success">変更　登録</button>
                            
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
