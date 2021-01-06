@extends('layouts.app')

@php

use Illuminate\Support\Arr;

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

#dump( $find );

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
                    @include( 'groupware.calendar.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">アクション</th>
                            <th class="">カレンダー名</th>
                            <th class="">備考</th>
                            <th class="">公開種別</th>
                            <th class="">アクセス権</th>
                            <th class="">デフォルト編集設定</th>
                            <th class="">not_use</th>
                            <th class="">disabled</th>
                        </tr>
                        @php
                            $class_new_schedule  = 'btn btn-sm btn-success';
                            $class_show_calprop  = 'btn btn-sm btn-outline btn-outline-secondary';
                            $class_show_calendar = 'btn btn-sm btn-warning';
                        
                        @endphp
                        @foreach( $calendars as $i => $calendar )
                            @php
                            
                                #$href = route( 'groupware.calendar.show', [ 'calendar' => $calendar->id ] );

                                $route_new_schedule  = route( 'groupware.schedule.create', [ 'calendar' => $calendar->id ] );
                                $route_show_calprop  = route( 'groupware.calprop.show',    [ 'calprop'  => $calendar->calprop()->id ] );
                                $route_show_calendar = route( 'groupware.calendar.show',   [ 'calendar' => $calendar->id ] );

                                $calprop = $calendar->calprop();
                                $style = "color: ". $calprop->text_color . "; background-color:" . $calprop->background_color . ";";
                                
                                if( $calendar->isOwner( $user->id )) {
                                    $authority = "管理者";
                                } elseif( $calendar->isWriter( $user->id )) {
                                    $authority = "予定追加可能";
                                } elseif( $calendar->isReader( $user->id )) {
                                    $authority = "予定閲覧のみ可";
                                } else {
                                    $authority = "権限なし";
                                }
                                $button = ( $calendar->canRead( $user->id )) ? "詳細・変更" : "詳細";
                                $disabled = "";
                                
                            @endphp
                        
                            <tr class="">
                                <td class="">
                                    @if( $calendar->canWrite( user_id() ) )
                                        <a class="{{ $class_new_schedule }}" href="{{ $route_new_schedule  }}">予定作成</a>
                                    @endif
                                    @if( $calendar->canRead( user_id() ) )
                                        <a class="{{ $class_show_calprop }}" href="{{ $route_show_calprop  }}">表示設定</a>
                                    @endif
                                    @if( $calendar->isOwner( user_id() ) )
                                        <a class="{{ $class_show_calendar }}" href="{{ $route_show_calendar }}">管理者設定</a>
                                    @endif
                                    {{ $calendar->id }}
                                </td>
                                <td class="">
                                    <span style="{{ $style }}" class="border border-round m-1 p-2">{{ $calendar->name }}</span>
                                </td>
                                <td class="">{{ $calendar->memo                 }}</td>
                                <td class="">{{ $authority                      }}</td>
                                <td class="">{{ $calendar->type                 }}</td>
                                <td class="">{{ $calprop->default_permission    }}</td>
                                <td class="">{{ $calendar->not_use              }}</td>
                                <td class="">{{ $calendar->disabled             }}</td>
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
