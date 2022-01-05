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

$types = Calendar::getTypes();
$default_permissions = Calendar::getDefaultPermissions();
$default_permissions['writers'] = '参加者・カレンダー編集者全員';

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
                    
                    <div class="table table-striped m-1 p-1 border clearfix container-fluid">
                        <div class="row no-gutters">
                            <div class="d-none d-md-block col-2 text-truncate bg-light">アクション</div>
                            <div class="d-none d-md-block col-2 text-truncate bg-light">カレンダー表示名 <span class="uitooltip" title="カレンダー名管理者設定">@icon( info-circle )</span></div>
                            <div class="d-none d-md-block col-2 text-truncate bg-light">アクセス権</div>
                            <div class="d-none d-md-block col-2 text-truncate bg-light">公開設定</div>
                            <div class="d-none d-md-block col-2 text-truncate bg-light">予定の変更権限<br>初期設定</div>
                            <div class="d-none d-md-block col-2 text-truncate bg-light">制限設定</div>
                            <div class="d-none d-md-block col-12">&nbsp;</div>

                            @php
                                $class_new_schedule  = 'btn btn-sm btn-success';
                                $class_show_calprop  = 'btn btn-sm btn-outline btn-outline-secondary';
                                $class_show_calendar = 'btn btn-sm btn-outline btn-outline-secondary';
                            @endphp
                            @foreach( $calendars as $i => $calendar )
                                @php
    
                                $calprop = $calendar->my_calprop();
                                                            
                                $route_new_schedule  = route( 'groupware.schedule.create', [ 'calendar' => $calendar->id ] );
                                $route_show_calprop  = route( 'groupware.calprop.show',    [ 'calprop'  => $calprop->id  ] );
                                $route_show_calendar = route( 'groupware.calendar.show',   [ 'calendar' => $calendar->id ] );

                                #$calprop = $calendar->calprop();
                                #dd( $calprop );
                                // $style = "color: ". $calprop->text_color . "; background-color:" . $calprop->background_color . ";";
                                $style = $calprop->style();
                                
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
                            
                                <div class="col-12 col-md-2 d-none d-md-block">
                                    @if( $calendar->canRead( user_id() ) )
                                        <a class="{{ $class_show_calprop }}" href="{{ $route_show_calprop  }}">表示設定</a>
                                    @endif
                                    @if( $calendar->isOwner( user_id() ) )
                                        <a class="{{ $class_show_calendar }}" href="{{ $route_show_calendar }}">管理者設定</a>
                                    @endif
                                    @if( 0 and $calendar->canWrite( user_id() ) and is_debug()  )
                                        <a class="{{ $class_new_schedule }}" href="{{ $route_new_schedule  }}">予定作成</a>
                                    @endif
                                    @if( is_debug() ) 
                                        <span class="uitooltip icon_debug m-1" title='calendar_id {{ $calendar->id }} calprop_id {{ $calprop->id }}'>
                                            <i class="fab fa-deploydog"></i>
                                        </span>
                                    @endif
                                    
                                </div>
                                <div class="col-12 d-block d-md-none my_label">カレンダー表示名</div>
                                <div class="col-10 col-md-2">
                                    <span style="{{ $style }}" class="border border-round m-md-1 p-md-2">{{ $calprop->name }}</span>
                                     @if( $calprop->name != $calendar->name )
                                        <span class="uitooltip" title="{{ $calendar->name }}">@icon( info-circle )</span>
                                    @endif
                                </div>
                                @if( $calendar->canRead( user_id() ) )
                                    <a class="btn d-block d-md-none col" href="{{ $route_show_calprop  }}" title="表示設定">@icon( search )</a>
                                @endif
                                @if( $calendar->isOwner( user_id() ) )
                                    <a class="btn d-block d-md-none col" href="{{ $route_show_calendar }}" title="管理者設定">@icon( config )</a>
                                @endif
                                <div class="col-4 col-md-2">{{ $authority                                                }}</div>
                                <div class="col-4 col-md-2">{{ op( $types )[$calendar->type]                             }}</div>
                                <div class="col-4 col-md-2">{{ op( $default_permissions )[$calprop->default_permission] }}
                                    @if( $calendar->default_permission != $calprop->default_permission )
                                        <span class="uitooltip" title='管理者設定： {{ op( $default_permissions )[$calendar->default_permission] }}'>
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-3 col-md-2">
                                    @if( $calendar->disabled )    <span class="alert-danger p-2">無効中</span>
                                    @elseif( $calendar->not_use ) <span class="alert-danger p-2"> 新規予定追加不可</span>
                                    @else &nbsp;
                                    @endif
                                </div>
                                
                                <div class="col-12 border border-light m-md-2"></div>
                            @endforeach
                        
                        </div>
                    </div>
                    
                    <hr>
                    <div class="m-1 w-100">{{ BackButton::form() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push( 'javascript' )
    <script>
        $( function() {  $('.uitooltip').uitooltip();  });
    </script>
@endpush


@stack( 'search_form_javascript' )
@stack( 'select_user_component_javascript' )
@stack( 'javascript' )

@endsection
