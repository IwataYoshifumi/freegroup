@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$user_id = auth( 'user' )->id();


@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.group.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.group.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">詳細・変更削除</th>
                            <th class="">グループ名</th>
                            <th class="">備考</th>
                        </tr>
                        
                        @foreach( $groups as $group )
                            @php
                                $href = route( 'groupware.group.show', [ 'group' => $group->id ] );
                                $used_owner = ( $group->check_if_the_user_in_this_is_owner( $user_id ) ) ? "アクセスリストで管理者設定あり" : "";
                                
                                $is_owner = $group->access_list()->isOwner( $user_id );
                                $button   = ( $is_owner ) ? "詳細・変更削除" : "詳細";
                                #dump( "/", $group->id, op( $group->access_list() )->id );
                            @endphp
                            <tr class="">
                                <td class="">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">{{ $button }}</a>
                                    {{ $group->id }} : {{ $used_owner }}
                                    
                                </td>
                                <td class="">{{ $group->name }}</td>
                                <td class="">{{ $group->memo }}</td>
                            </tr>
                        @endforeach
                        
                    </table>

                    <div class="w-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stack( 'search_form_javascript' )
@stack( 'select_user_component_javascript' )

@endsection
