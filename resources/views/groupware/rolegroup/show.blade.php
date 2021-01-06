@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelpler;

$lists = toArrayKeyIncremental( $role_group->lists, 'role' );
$users = $role_group->users()->with(['dept'])->get();
$array_role_list = RoleList::getRoles();

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.rolegroup.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                        @if( Route::currentRouteName() == "groupware.role_group.show" )
                            <a class="btn btn-warning text-dark" href="{{ route( 'groupware.role_group.update', [ 'role_group' => $role_group ] ) }}">ロールグループの修正</a>
                            @if( ! $role_group->default )
                                <a class="btn btn-danger text-white" href="{{ route( 'groupware.role_group.delete', [ 'role_group' => $role_group ] ) }}">ロールグループ削除</a>
                            @endif
                        @endif
                        
                        
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <div class="col-12 m-1"></div>
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">ロールグループ名</label>
                            <div class="col-md-6 m-1">
                                {{ $role_group->name }}
                            </div>
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                            <div class="col-md-6 m-1">
                                {{ $role_group->memo }}
                            </div>
                            
                            @if( $role_group->default )
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">デフォルト</label>
                                <div class="col-md-6 m-1">
                                    <b>レ</b>
                                    <span class="alert-warning m-1">初期設定値は削除できません。</span>
                                </div>
                            @endif

                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">ロール設定</label>
                            <div class="col-md-6">
                                <div class="row">
                                    @foreach( $array_role_list as $role => $memo )
                                        <div class="col-md-1 m-1">
                                            @if( in_array( $role, $lists, 1 ))
                                                ■
                                                @php $font = "font-weight-bold"; @endphp
                                            @else 
                                                □
                                                @php $font = "text-secondary"; @endphp
                                            @endif
                                        </div>
                                        <div class="col-md-10 m-1 {{ $font }}">{{ $memo }}</div>
                                    
                                    @endforeach
                                </div>
                            </div>

                            <hr>
                            <table class="table table-striped col-11 m-3 p-1">
                                <tr>
                                    <th>ロール割当修正</th>
                                    <th>社員名</th>
                                    <th>部署名</th>
                                    <th>役職</th>
                                </tr>
                                @foreach( $users as $user )
                                    @php
                                        $href = route( 'groupware.role_group.attach_role', [ 'users[]' => $user->id ] );
                                    @endphp
                                    <tr>
                                        <td><a class="btn btn-secondary btn-sm m-1" href="{{ $href }}">ロール変更</a></td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->dept->name }}</td>
                                        <td>{{ $user->grade }}</td>
                                        
                                    </tr>
                                @endforeach
                            </table>
                                
                                
                            
                            
                        </div>

                        <div class="form-group row mb-0">
                            @if( Route::currentRouteName() == "groupware.role_group.delete" )
                                <form id="delete_form" method="POST" action="{{ url()->current() }}" class="col-12">
                                    @method( 'DELETE' )
                                    @csrf
                                    <div class="row p-1">
                                        {{ Form::checkbox( 'comfirm', 1, 0, [ 'class' => 'col-1 m-1' ] ) }} <div class=" col-4 text-danger font-weight-bold m-1">削除確認</div>
                                        <div class="col-12"></div>
                                    </div>
                                    <br>
                                </form>
                                <script>
                                    function submit_button() {
                                        $('#delete_form').submit();
                                    }
                                </script>
                                <a class="btn btn-danger text-white ml-5" onClick="submit_button();">ロールグループ削除</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
