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
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">詳細・変更削除</th>
                            <th class="">アクセスリスト名</th>
                            <th class="">アクセス権</th>
                            <th class="">備考</th>
                        </tr>
                        @foreach( $access_lists as $i => $access_list )
                            @php
                                $href = route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ] );
                                #$disabled = ( empty( $acccess_list->role )) ? "disabled" : "";      
                                #$disabled = ( empty( $acccess_list->role ) or ( $access_list->role == "freeBusyReader" )) ? "disabled" : "";      
                                $disabled = "";
                                $button = ( $access_list->isOwner( user_id() )) ? "詳細・変更" : "詳細";
                                
                            @endphp
                        
                            <tr class="">
                                <td class="">
                                    <a class="btn btn-sm btn-outline-secondary {{ $disabled }}" href="{{ $href }}">{{ $button }}</a>
                                    {{ $access_list->id }}
                                </td>
                                <td class="">{{ $access_list->name }}</td>
                                <td class="">{{ $array_roles[$access_list->role] }}</td>
                                
                                <td class="">{{ $access_list->memo }}</td>
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
