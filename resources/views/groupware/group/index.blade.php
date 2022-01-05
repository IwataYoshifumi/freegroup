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
                    
                    <div class="w-95 table table-striped">
                        <div class="row">
                            <div class="d-none d-md-block col-2">詳細・変更削除</div>
                            <div class="d-block col-5 col-md-4">グループ名</div>
                            <div class="d-block col-5 col-md-6">備考</div>
                            
                            @foreach( $groups as $group )
                                @php
                                    $href = route( 'groupware.group.show', [ 'group' => $group->id ] );
                                    $is_owner = $group->access_list()->isOwner( $user_id );
                                    $button   = ( $is_owner ) ? "詳細・変更" : "詳細";
    
                                @endphp
                                <a class="d-none d-md-block col-2 btn btn-sm btn-outline-secondary text-truncate" href="{{ $href }}">{{ $button }}</a>

                                @if( $group->check_if_the_user_in_this_is_owner( $user_id )  )
                                    <span class="btn_icon uitooltip" title="アクセスリストの管理者設定に使われて言います">@icon( exclamation-triangle )</span>
                                @endif
                                <div class="d-none  d-md-block col-4 text-truncate">{{ $group->name }}</div>
                                <div class="d-block d-md-none  col-6 text-truncate"><a class="" href="{{ $href }}">{{ $group->name }}</a></div>

                                <div class="d-block col-5 col-md-6 text-truncate">{{ $group->memo }}</div>
                            @endforeach
                        </div>
                        
                    </div>

                    <div class="w-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stack( 'search_form_javascript' )
@stack( 'select_user_component_javascript' )

@endsection
