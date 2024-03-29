@extends('layouts.app')

@php

use Illuminate\Support\Arr;

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\Http\Helpers\BackButton;

$array_roles = ACL::get_array_roles_for_select();
$array_roles[''] = '-';

$user = auth( 'user' )->user();

$types = TaskList::getTypes();
$default_permissions = TaskList::getDefaultPermissions();
$default_permissions['writers'] = '参加者・タスクリスト編集者全員';

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.tasklist.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.tasklist.index_search' )
                    
                    <div class="table table-striped m-1 p-1 border clearfix">
                        <div class="row no-gutters">
                            
                            <div class="d-none d-md-block col-2">アクション</div>
                            <div class="d-none d-md-block col-2">タスクリスト表示名 <span class="uitooltip" title="タスクリスト名管理者設定">@icon( info-circle )</span></div>
                            <div class="d-none d-md-block col-2">アクセス権</div>
                            <div class="d-none d-md-block col-2">公開設定</div>
                            <div class="d-none d-md-block col-2">予定の変更権限<br>初期設定</div>
                            <div class="d-none d-md-block col-2">制限設定</div>

                            @php
                                $class_new_schedule  = 'btn btn-sm btn-success';
                                $class_show_taskprop  = 'btn btn-sm btn-outline btn-outline-secondary';
                                $class_show_tasklist = 'btn btn-sm btn-outline btn-outline-secondary';
                            
                            @endphp
                            @foreach( $tasklists as $i => $tasklist )
                                @php
                                    $taskprop = $tasklist->my_taskprop();
                                    $style = $taskprop->style();
     
                                    $route_new_schedule  = route( 'groupware.schedule.create', [ 'tasklist' => $tasklist->id ] );
                                    $route_show_taskprop  = route( 'groupware.taskprop.show',    [ 'taskprop'  => $taskprop->id ] );
                                    $route_show_tasklist = route( 'groupware.tasklist.show',   [ 'tasklist' => $tasklist->id ] );
    
                                    if( $tasklist->isOwner( $user->id )) {
                                        $authority = "管理者";
                                    } elseif( $tasklist->isWriter( $user->id )) {
                                        $authority = "予定追加可能";
                                    } elseif( $tasklist->isReader( $user->id )) {
                                        $authority = "予定閲覧のみ可";
                                    } else {
                                        $authority = "権限なし";
                                    }
                                    $button = ( $tasklist->canRead( $user->id )) ? "詳細・変更" : "詳細";
                                    $disabled = "";
                                    
                                @endphp
                            
                                <div class="col-12 col-md-2 d-none d-md-block">
                                    @if( $tasklist->canRead( user_id() ) )
                                        <a class="{{ $class_show_taskprop }}" href="{{ $route_show_taskprop  }}">表示設定</a>
                                    @endif
                                    @if( $tasklist->isOwner( user_id() ) )
                                        <a class="{{ $class_show_tasklist }}" href="{{ $route_show_tasklist }}">管理者設定</a>
                                    @endif
                                    @if( 0 and $tasklist->canWrite( user_id() ) and is_debug()  )
                                        <a class="{{ $class_new_schedule }}" href="{{ $route_new_schedule  }}">予定作成</a>
                                    @endif
                                    @if( is_debug() ) 
                                        <span class="uitooltip icon_debug m-1" title='tasklist_id {{ $tasklist->id }} taskprop_id {{ $taskprop->id }}'>
                                            <i class="fab fa-deploydog"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-12 d-block d-md-none my_label">タクスリスト名</div>
                                <div class="col-10 col-md-2 text-truncate">
                                    <span style="{{ $style }}" class="border border-round">{{ $taskprop->name }}</span>
                                    @if( $taskprop->name != $tasklist->name )
                                        <span class="uitooltip" title="{{ $tasklist->name }}">@icon( info-circle )</span>
                                    @endif
                                </div>
                                @if( $tasklist->canRead( user_id() ))
                                    <a class="btn col d-block d-md-none" href="{{ $route_show_taskprop }}">@icon( search )</a>
                                @endif
                                @if( $tasklist->isOwner( user_id() ))
                                    <a class="btn col d-block d-md-none" href="{{ $route_show_tasklist }}">@icon( config )</a>
                                @endif
                                
                                
                                <div class="col-3 col-md-2 text-truncate">{{ $authority                                                }}</div>
                                <div class="col-4 col-md-2 text-truncate">{{ op( $types )[$tasklist->type]                             }}</div>
                                <div class="col-3 col-md-2 text-truncate">{{ op( $default_permissions )[$taskprop->default_permission] }}
                                    @if( $tasklist->default_permission != $taskprop->default_permission )
                                        <span class="uitooltip" title='管理者設定： {{ op( $default_permissions )[$tasklist->default_permission] }}'>
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-2 col-md-2 text-truncate">
                                    @if( $tasklist->disabled )    <span class="alert-danger p-2">無効中</span>
                                    @elseif( $tasklist->not_use ) <span class="alert-danger p-2"> 新規予定追加不可</span>
                                    @else &nbsp;
                                    @endif
                                </div>
                                <div class="col-12 border border-light m-1"></div>
                            @endforeach
                        </div>                        
                    </div>

                    <hr>
                    <div class="w-100 m-1">{{ BackButton::form() }}</div>
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
