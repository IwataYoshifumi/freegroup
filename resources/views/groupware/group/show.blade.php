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


$delete_button_disabled = ( ! user()->can( 'delete', $group ) ) ? "disabled" : "";


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
                            <a class="btn btn-warning text-dark" href="{{ route( 'groupware.group.update', [ 'group' => $group ] ) }}">グループの修正</a>
                            <a class="btn btn-danger text-white {{ $delete_button_disabled }}" 
                               href="{{ route( 'groupware.group.delete', [ 'group' => $group ] ) }}"
                               data-toggle="tooltip" 
                               title="アクセスリストにグループが使われているため削除できません"
                            >グループ削除</a>
                        @endcan
                        @cannot( 'delete', $group )
                            <script>
                                $(document).ready( function() {
                                    $('[data-toggle="tooltip"]').tooltip();
                                });
                            </script>
                        @endcannot


                        @if( $group->check_if_the_user_in_this_is_owner( user_id() ) )
                            <div class="alert-warning m-1 p-1">このグループであなたを管理者設定しているアクセスリストがあります。グループ設定を変えるときは注意してください。</div>                                
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
