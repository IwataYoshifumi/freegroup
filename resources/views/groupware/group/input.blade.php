@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\GetAccessLists;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$select_depts = toArrayWithEmpty( Dept::all() );

//　ユーザが管理者になっているアクセスリストを検索
//
#$access_lists = GetAccessLists::isOwner( auth( 'user' )->id() );
$access_lists = AccessList::whereOwner( auth('user')->id() )->get();
$select_access_lists = toArrayWithEmpty( $access_lists );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.group.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )
                    
                    <div class="col-12">
                        <form method="POST" action="{{ url()->full() }}">
                            @csrf
    
                            <div class="form-group row">
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">グループ名</label>
                                <div class="col-md-6">
                                    {{ Form::text( 'name', old( 'name', optional( $group )->name ), ['class' => 'form-control m-1', 'required' ] ) }}
                                </div>
                                
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                                <div class="col-md-6">
                                    {{ Form::text( 'memo', old( 'memo',  optional( $group )->memo ), ['class' => 'form-control m-1' ] ) }}
                                </div>
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
                                <div class="col-md-6">
                                    {{ Form::select( 'access_list_id', $select_access_lists, optional( $group )->access_list_id(), [ 'class' => 'form-control m-1' ] ) }}
                                </div>
                                
                                
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">グループ所属ユーザ</label>
                                <div class="col-md-6">
                                    <x-input_users :users="$users"/>
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
