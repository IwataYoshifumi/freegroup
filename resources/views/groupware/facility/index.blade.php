@extends('layouts.app')

@php

use Illuminate\Support\Arr;

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Facility;

use App\Http\Helpers\BackButton;

$array_roles = ACL::get_array_roles_for_select();
$array_roles[''] = '-';

$user = auth( 'user' )->user();

$types = Facility::getTypes();
$default_permissions = Facility::getDefaultPermissions();
$default_permissions['writers'] = '参加者・設備編集者全員';

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.facility.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.facility.index_search' )
                    
                    {{ Form::open( [ 'url' => route( 'groupware.reservation.create' ), 'medivod' => 'GET', 'id' => 'select_facilities_form' ] ) }}
                        @csrf
                        <div class="table table-striped m-1 p-1 border clearfix">
                            <div class="row no-gutters">
                                <div class="d-none d-md-block col-3">アクション</div>
                                <div class="d-none d-md-block col-2">設備表示名</div>
                                <div class="d-none d-md-block col-2">分類</div>
                                <div class="d-none d-md-block col-2">アクセス権</div>
                                <div class="d-none d-md-block col-2">公開種別</div>
                                <div class="d-none d-md-block col-1 text-truncate">無効化</div>
    
                                @php
                                $can_reserve = 0;                            
                                @endphp
    
                                @foreach( $facilities as $i => $facility )
                                    @php
                                        $style = $facility->style();
         
                                        $route_edit_facility   = route( 'groupware.facility.update', [ 'facility' => $facility->id ] );
                                        $route_show_facility   = route( 'groupware.facility.show'  , [ 'facility' => $facility->id ] );
                                        $route_delete_facility = route( 'groupware.facility.delete', [ 'facility' => $facility->id ] );
                                        $route_to_check_availability = '';
        
                                        if( $facility->isOwner( $user->id )) {
                                            $authority = "管理者";
                                        } elseif( $facility->isWriter( $user->id )) {
                                            $authority = "予定追加可能";
                                        } elseif( $facility->isReader( $user->id )) {
                                            $authority = "予定閲覧のみ可";
                                        } else {
                                            $authority = "権限なし";
                                        }
                                        $button = ( $facility->canRead( $user->id )) ? "詳細・変更" : "詳細";
                                        $disabled = "";
                                        
                                    @endphp
                                
                                    <div class="col-12 col-md-3">
    
                                        @if( $user->can( 'reserve', $facility ))
                                            @php
                                            $id = "facility_" . $facility->id;
                                            $can_reserve = 1;
                                            @endphp
                                        
                                            <label for="{{ $id }}">設備予約</label>
                                            {{ Form::checkbox( "facilities[]", $facility->id, 0, [ 'id' => $id, 'class' => 'checkboxradio facility' ] ) }}
                                        @endif
    
                                        @can( 'view', $facility  )
                                            <a class="btn icon_btn" href="{{ $route_show_facility }}" title="詳細">@icon( search )</a>
                                        @endcan
                                        
                                        @if( $facility->isOwner( user_id() ) )
                                            <a class="btn icon_btn" href="{{ $route_edit_facility }}" title="変更">@icon( edit )</a>
                                        @endif
                                        
                                        @can( 'delete', $facility )
                                            <a class="btn icon_btn text-danger" href="{{ $route_delete_facility }}" title="削除">@icon( trash )</a>
                                        @endcan
                                        
                                    </div>

                                    <div class="d-block d-md-none col-3">設備名：</div>
                                    <div class="col-9 col-md-2 text-truncate">
                                        <span style="{{ $style }}" class="border border-round m-1 p-2">{{ $facility->name }}</span>
                                        @if( $facility->name != $facility->name )
                                            <span class="uitooltip" title="{{ $facility->name }}">@icon( info-circle )</span>
                                        @endif
                                    </div>

                                    <div class="d-block d-md-none col-3">分類：</div>
                                    <div class="col-9 col-md-2">
                                        {{ $facility->category }}
                                        @if( ! is_null( $facility->sub_category )) 
                                            （{{ $facility->sub_category }}）                                    
                                        @endif
                                    </div>
                                    
                                    <div class="d-block d-md-none col-3">権限：</div>
                                    <div class="col-4 col-md-2 text-truncate">{{ $authority                                                }}</div>
                                    <div class="col-4 col-md-2 text-truncate">{{ op( $types )[$facility->type]                             }}</div>
                                    <div class="col-1 col-md-1 text-truncate">
                                        @if( $facility->disabled )    <span class="alert-danger p-2">無効設備</span>
                                        @elseif( $facility->not_use ) <span class="alert-danger p-2"> 新規予約不可</span>
                                        @else &nbsp;
                                        @endif
                                    </div>
                                    <div class="d-block d-md-none col-11 border border-secaondary m-2"></div>
                                    
                                    @if( $loop->last and $can_reserve )
                                        <div class="col-12 mt-2">
                                            <a class="btn btn-success text-white" onClick="form_submit()">設備予約（空き状況確認）</a>
                                        </div>                                        
                                        <script>
                                            function form_submit() {
                                                console.log( 'aaa' );
                                                
                                                var checked = false;
                                                $('.facility').each( function() {
                                                    console.log( $(divis).val(), $(divis).prop('checked') );
                                                    if( $(divis).prop( 'checked' )) { checked = true; }
                                                });
                                                if( checked ) { 
                                                    $('#select_facilities_form').submit();
                                                } else {
                                                    alert( '予約する設備を選択してください');
                                                }        
                                            }                               
                                        </script>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    {{ Form::close() }}
                    
                    <hr>
                    <div class="w-100 m-1">{{ BackButton::form() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
