@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.rolegroup.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )
                    
                        <form method="POST" action="{{ url()->full() }}">
                        @csrf

                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">ロールグループ名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', optional( $role_group )->name ), ['class' => 'form-control m-1', 'required' ] ) }}
                            </div>
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  optional( $role_group )->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">デフォルト</label>
                            <div class="col-md-6 m-1">
                                {{ Form::checkbox( 'default', TRUE, old( 'default',  optional( $role_group )->default ), ['class' => 'm-1' ] ) }} 
                                ユーザ初期設定
                            </div>
                            
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">ロール設定</label>
                            <div class="col-md-6">
                                <div class="row">
                                    @foreach( RoleList::get_array_role_lists() as $role => $memo )
                                        <div class="col-md-1 m-1">
                                            {{ Form::checkbox( 'lists[]', $role, in_array( $role, $lists, 1 ) , [ 'class' => '' ] ) }}
                                        </div>
                                        <div class="col-md-10 m-1">{{ $memo }}</div>
                                    
                                    @endforeach
                                </div>


                            </div>

                            
                            
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">登録</button>
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
