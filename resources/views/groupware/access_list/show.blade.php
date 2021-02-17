@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

#dump( $roles );
#dump( $role_list );
#dump( $access_list, $acls );
#dump( $access_list, $lists, $users, $access_list->users );

$auth = user();

$disabled         = ( ! $auth->can( 'delete', $access_list ) ) ? "disabled" : "";
$title_delete_btn = ( ! $disabled ) ? "アクセスリスト削除" : "アクセスリスト設定先があるため削除できません" ;

$route_to_edit   = route( 'groupware.access_list.update', [ 'access_list' => $access_list ] );
$route_to_delete = route( 'groupware.access_list.delete', [ 'access_list' => $access_list ] );
if_debug( $title_delete_btn );
@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.access_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                        @can( 'update', $access_list )
                            <a class="btn btn_icon uitooltip" href="{{ $route_to_edit   }}" title="アクセスリストの修正"   >@icon( edit  )</a>
                            @if( $auth->can( 'delete', $access_list ))
                                @php $title = "アクセスリスト削除"; @endphp
                                <a class="btn btn_icon uitooltip" href="{{ $route_to_delete }}" title="{{ $title }}">@icon( trash )</a>
                            @else
                                @php $title = "アクセスリスト設定先があるため削除できません"; @endphp
                                <a class="btn btn_icon uitooltip text-secondary" title="{{ $title }}">@icon( trash )</a>
                            @endif
                        @endcan
                    
                        
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.access_list.show_parts' )
                            
                            
                        </div>

                        {{ BackButton::form() }}


                </div>
            </div>
        </div>
    </div>
</div>

@endsection
