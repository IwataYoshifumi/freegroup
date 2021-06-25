@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

use App\Http\Helpers\BackButton;

$array_roles = ACL::get_array_roles_for_select();
$array_roles[''] = '-';

$user = auth( 'user' )->user();

#dump( $calendars );
@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.calendar.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    include( 'groupware.calendar.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">詳細・変更削除</th>
                            <th class="">カレンダー名</th>
                            <th class="">備考</th>
                            <th class="">アクセス権</th>
                            <th class="">not_use</th>
                            <th class="">disabled</th>
                        </tr>
                        @foreach( $calendars as $i => $calendar )
                            @php
                                $href = route( 'groupware.calendar.show', [ 'calendar' => $calendar->id ] );
                                
                                if( $calendar->isOwner( $user->id )) {
                                    $authority = "管理者";
                                } elseif( $calendar->isWriter( $user->id )) {
                                    $authority = "スケジュール追加可能";
                                } elseif( $calendar->isReader( $user->id )) {
                                    $authority = "スケジュール閲覧のみ可能";
                                } else {
                                    $authority = "権限なし";
                                }
                                $button = ( $calendar->isOwner( $user->id )) ? "詳細・変更" : "詳細";
                                $disabled = "";
                            @endphp
                        
                            <tr class="">
                                <td class="">
                                    <a class="btn btn-sm btn-outline-secondary {{ $disabled }}" href="{{ $href }}">{{ $button }}</a>
                                    {{ $calendar->id }}
                                </td>
                                <td class="">{{ $calendar->name }}</td>
                                <td class="">{{ $calendar->memo }}</td>
                                <td class="">{{ $authority }}</td>
                                <td class="">{{ $calendar->not_use  }}</td>
                                <td class="">{{ $calendar->disabled }}</td>
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
