@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\Helper;

$select_roles[''] = ''; 
$select_roles = array_merge( $select_roles, ACL::getRoleTypes() );

$select_depts = toArrayWithNull( Dept::all() );
$select_groups = toArrayWithNull( Group::all() );
$select_types = [ '' => '', 'user' => 'ユーザ', 'dept' => '部署', 'group' => 'グループ' ];

#$old_users = old( 'users' );
#dump( $old_users );
@endphp

@section('content')

@include( 'groupware.access_list.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.access_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                    @if( $errors->count() )
                        <div class="alert-warning">
                            入力エラーで順序が元に戻っているます。確認してください。
                        </div>
                    @endif
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                    
                
                    <form method="POST" action="{{ url()->full() }}">
                    @csrf
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト名</label>
                        <div class="col-md-6">
                            {{ Form::text( 'name', old( 'name', optional( $access_list )->name ), ['class' => 'form-control m-1', ] ) }}
                        </div>
                        
                        <label for="memo" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                        <div class="col-md-6">
                            {{ Form::text( 'memo', old( 'memo',  optional( $access_list )->memo ), ['class' => 'form-control m-1' ] ) }}
                        </div>
                        
                        @error( 'IacceptNotOwner' )
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">同意</label>
                            <div class="col-md-6 alert-danger">
                                {{ Form::checkbox( 'IacceptNotOwner', 1, [ 'class' => 'form-control' ] ) }}
                                自分自身が管理権限なく、以後このアクセスリストが編集できません。
                            </div>
                        @enderror
                        
                        <label class="col-md-12 col-form-label text-md-left m-1">ロール設定</label>
                        <div class="col-md-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>順序</th>
                                        <th>権限</th>
                                        <th>種別</th>
                                        <th>部署・グループ・ユーザ</th>
                                    </tr>
                                </thead>
                                <tbody id="sortdata">
                                    @foreach( old( 'orders', $orders ) as $j => $i )
                                        <tr class="form_row" data-id="{{ $i }}">
                                            <td><span name="num_data">{{ $i }}</span>
                                                {{--
                                                 {{ $j }}-{{ $i }} new_
                                                 --}}
                                                <input type=hidden name="orders[]" value="{{ $i }}" class='order_input' 'id'="order_{{ $i }}", data-id="{{ $i }}">
                                            </td>
                                            
                                            <td>
                                                {{ Form::select( "roles[$i]", $select_roles, optional($roles)[$i], 
                                                    [ 'class' => 'form-control role_select', 'id' => "role_$i", 'data-id' => $i ] )}}
                                            </td>
                                            <td>
                                                {{ Form::select( "types[$i]", $select_types, optional($types)[$i], 
                                                    [ 'class' => 'form-control select_types', 'id' => "type_$i", 'data-id' => $i ] )}}
                                            </td>
                                            <td>
                                                {{-- 部署フォーム --}}
                                                <div class="list_type_{{ $i }}" data-type="dept">
                                                    {{ Form::select( "depts[$i]", $select_depts, optional($depts)[$i], 
                                                        [ 'class' => 'form-control' ] )}}
                                                </div>
                                                
                                                {{-- グループ　フォーム --}}                                                    
                                                <div class="list_type_{{ $i }}" data-type="group">
                                                    {{ Form::select( "groups[$i]", $select_groups, optional($groups)[$i], 
                                                        [ 'class' => 'form-control' ] )}}
                                                </div>
                                                
                                                {{-- ユーザ　フォーム --}}
                                                @php
                                                    $array = [  'form_name' => "users[$i]", 
                                                                'user_id'   => optional( $users )[$i],
                                                                'index'     => $i,
                                                            ];
                                                            #$old_user = "users[". $i ."]";
                                                            #dump( $old_user, $old_users[$i], old( $old_user ) , optional( $users )[$i] );
                                                            #dump( old( "depts[$i]" ) , optional( $depts )[$i] );
                                                    
                                                @endphp
                                                <div class="list_type_{{ $i }}" data-type="user">
                                                    
                                                    <x-select_user :array="$array" />                                                        

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
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


@stack( 'select_user_component_javascript' )
@stack( 'javascript' )

@endsection
