@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Depts;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelpers;

$auth = user();

$route_to_edit   = route( 'groupware.group.update', [ 'group' => $group ] );
$route_to_delete = route( 'groupware.group.delete', [ 'group' => $group ] );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.group.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                        @can( 'update', $group )
                            <a class="btn btn_icon text-dark uitooltip" title="グループ修正" href="{{ $route_to_edit }}">@icon( edit )</a>
                            @if( $auth->can( 'delete', $group ))
                                <a class="btn btn_icon uitooltip" href="{{ $route_to_delete }}" title="グループ削除">@icon( trash )</a>
                            @else
                                <a class="btn btn_icon text-secondary uitooltip" title="アクセスリストで利用中なため削除できません">@icon( trash )</a>
                            @endif
                        @endcan

                        @if( $group->check_if_the_user_in_this_is_owner( $auth->id ) )
                            <div class="alert-warning m-1 p-1">このグループであなたを管理者設定しているアクセスリストがあります。あなたをグループから削除するとアクセスリストの管理者権限を失いますので、注意してください。</div>                                
                        @endif

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.group.show_parts' )

                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
