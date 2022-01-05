@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$array_roles = ACL::get_array_roles_for_select();
$array_roles[''] = '-';

$user = auth( 'user' )->user();

#dump( $access_lists );
@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.access_list.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.access_list.index_search' )
                    
                    <div class="table table-striped m-1 p-1 border clearfix">
                        <div class="row">
                            <div class="col-2 d-none d-md-block font-weight-bold">詳細・変更削除</div>
                            <div class="col-8 col-md-4 font-weight-bold">アクセスリスト名</div>
                            <div class="col-4 col-md-2 font-weight-bold">
                                <span class="d-none d-md-block">自分のアクセス権</span>
                                <span class="d-block d-md-none">アクセス権</span>
                            </div>
                            <div class="col-4 d-none d-md-block font-weight-bold">備考</div>

                            @foreach( $access_lists as $i => $access_list )
                                @php
                                    $href = route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ] );
                                    #$disabled = ( empty( $acccess_list->role )) ? "disabled" : "";      
                                    #$disabled = ( empty( $acccess_list->role ) or ( $access_list->role == "freeBusyReader" )) ? "disabled" : "";      
                                    $button = ( $access_list->isOwner( user_id() )) ? "詳細・変更" : "詳細";
                                    $role = op( $access_list->user_roles->first() )->role;
                                    
                                @endphp
                            
                                <div class="col-2 d-none d-md-block">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">{{ $button }}</a>
                                </div>
                                <div class="col-8 col-md-4 text-truncate">
                                    <a class="d-block d-md-block btn text-left" href="{{ $href }}">{{ $access_list->name }}</a>
                                    <span class="d-none d-md-none">{{ $access_list->name }}</span>
                                </div>
                                <div class="col-4 col-md-2">{{ $array_roles[$role] }}
                                
                                </div>
                                
                                <div class="col-4 d-none d-md-block">{{ $access_list->memo }}</div>
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
