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
            @include( 'groupware.reservation.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.reservation.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">アクション</th>
                            <th class="">予約者</th>
                            <th class="">予約目的</th>
                            <th class="">設備名</th>
                            <th class="">分類</th>
                            <th class="">予約日時</th>
                        </tr>
                        @php
                        $class_show_reservation = 'btn btn_icon';
                        
                        @endphp
                            @foreach( $reservations as $i => $reservation )
                                @php
                                $style = $reservation->style();
 
                                $route_edit_reservation   = route( 'groupware.reservation.update', [ 'reservation' => $reservation->id ] );
                                $route_show_reservation   = route( 'groupware.reservation.show'  , [ 'reservation' => $reservation->id ] );
                                $route_delete_reservation = route( 'groupware.reservation.delete'  , [ 'reservation' => $reservation->id ] );
                                $route_to_check_availability = '';
                                
                                $data = "class='object_to_show_detail' style='cursor: pointer;' data-object='reservation' data-object_id=" . $reservation->id;
                                @endphp
                            
                                <tr>
                                    <td>
                                        @can( 'view', $reservation  )
                                            <a class="{{ $class_show_reservation }}" href="{{ $route_show_reservation }}" title="詳細表示">@icon( schedule )</a>
                                        @endcan
                                        
                                        @can( 'update', $reservation )
                                            <a class="{{ $class_show_reservation }}" href="{{ $route_edit_reservation }}" title="変更">@icon( edit )</a>
                                        @endcan
                                        @can( 'delete', $reservation )
                                            <a class="{{ $class_show_reservation }}" href="{{ $route_delete_reservation }}" title="予約キャンセル">@icon( trash )</a>
                                        @endcan

                                    </td>
                                    <td {!! $data !!}>{{ $reservation->user->name  }}</td> {{-- htmlspecialchars OK --}}
                                    <td {!! $data !!}>{{ $reservation->purpose }}</td> {{-- htmlspecialchars OK --}}
                                    <td {!! $data !!}>{{ $reservation->facility->name }}</td> {{-- htmlspecialchars OK --}}
                                    <td {!! $data !!}> {{-- htmlspecialchars OK --}}
                                        {{ $reservation->facility->category }}
                                        @if( ! is_null( $reservation->facility->sub_category )) 
                                            （{{ $reservation->facility->sub_category }}）                                    
                                        @endif
                                    </td>
                                    <td {!! $data !!}> {{ $reservation->p_time('index') }}</td>
                                </tr>
                            @endforeach
                    </table>

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

<!-- 詳細表示モーダルウィンドウ -->
@include( 'groupware.show_all.modal_to_show_detail' )

@stack( 'search_form_javascript' )
@stack( 'select_user_component_javascript' )
@stack( 'javascript' )

@endsection
