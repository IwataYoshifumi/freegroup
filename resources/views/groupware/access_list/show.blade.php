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

$disabled = ( ! user()->can( 'delete', $access_list ) ) ? "disabled" : "";


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
                                <a class="btn btn-warning text-dark" href="{{ route( 'groupware.access_list.update', [ 'access_list' => $access_list ] ) }}">アクセスリストの修正</a>
                                <a class="btn btn-danger text-white {{ $disabled }}" href="{{ route( 'groupware.access_list.delete', [ 'access_list' => $access_list ] ) }}">アクセスリスト削除</a>
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
