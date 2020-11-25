@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.rolegroup.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.rolegroup.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">詳細・変更削除</th>
                            <th class="">ロールグループ名</th>
                            <th class="">初期値</th>
                            <th class="">備考</th>
                        </tr>
                        
                        @foreach( $role_groups as $role_group )
                            <tr class="">
                                <td class="">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route( 'groupware.role_group.show', [ 'role_group' => $role_group ] ) }}">詳細・変更削除</a>
                                </td>
                                <td class="">{{ $role_group->name     }}</td>
                                <td class="">
                                    @if( $role_group->default )
                                        ●
                                    @else 
                                        &nbsp;
                                    @endif
                                
                                </td>
                                <td class="">{{ $role_group->memo     }}</td>
                            </tr>
                        @endforeach
                    </table>

                    <div class="w-100"></div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
