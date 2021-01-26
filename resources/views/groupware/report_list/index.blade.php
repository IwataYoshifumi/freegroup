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
            @include( 'groupware.report_list.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    include( 'groupware.report_list.index_search' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">アクション</th>
                            <th class="">日報リスト名</th>
                            <th class="">備考</th>
                            <th class="">公開種別</th>
                            <th class="">アクセス権</th>
                            <th class="">デフォルト編集設定</th>
                            <th class="">not_use</th>
                            <th class="">disabled</th>
                        </tr>
                        @php
                            $class_new_report       = 'btn btn-sm btn-success';
                            $class_show_report_prop = 'btn btn-sm btn-outline btn-outline-secondary';
                            $class_show_report_list = 'btn btn-sm btn-outline btn-outline-secondary';
                        
                        @endphp
                        @foreach( $report_lists as $i => $report_list )
                            @php
                            
                                #$href = route( 'groupware.report_list.show', [ 'report_list' => $report_list->id ] );

                                $route_new_report  = route( 'groupware.report.create', [ 'report_list' => $report_list->id ] );
                                $route_show_report_prop  = route( 'groupware.report_prop.show',    [ 'report_prop'  => $report_list->report_prop()->id ] );
                                $route_show_report_list = route( 'groupware.report_list.show',   [ 'report_list' => $report_list->id ] );

                                $report_prop = $report_list->report_prop();
                                $style = "color: ". $report_prop->text_color . "; background-color:" . $report_prop->background_color . ";";
                                
                                if( $report_list->isOwner( $user->id )) {
                                    $authority = "管理者";
                                } elseif( $report_list->isWriter( $user->id )) {
                                    $authority = "予定追加可能";
                                } elseif( $report_list->isReader( $user->id )) {
                                    $authority = "予定閲覧のみ可";
                                } else {
                                    $authority = "権限なし";
                                }
                                $button = ( $report_list->canRead( $user->id )) ? "詳細・変更" : "詳細";
                                $disabled = "";
                                
                            @endphp
                        
                            <tr class="">
                                <td class="">

                                    @if( $report_list->canRead( user_id() ) )
                                        <a class="{{ $class_show_report_prop }}" href="{{ $route_show_report_prop  }}">表示設定</a>
                                    @endif
                                    @if( $report_list->isOwner( user_id() ) )
                                        <a class="{{ $class_show_report_list }}" href="{{ $route_show_report_list }}">管理者設定</a>
                                    @endif
                                    @if( 0 and $report_list->canWrite( user_id() ) )
                                        <a class="{{ $class_new_report }}" href="{{ $route_new_report  }}">予定作成</a>
                                    @endif
                                    @if( is_debug() ) 
                                        <span class="uitooltip icon_debug m-1" title='report_list_id {{ $report_list->id }} report_prop_id {{ $report_prop->id }}'>
                                            <i class="fab fa-deploydog"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="">
                                    <span style="{{ $style }}" class="border border-round m-1 p-2">{{ $report_list->name }}</span>
                                </td>
                                <td class="">{{ $report_list->memo                 }}</td>
                                <td class="">{{ $authority                      }}</td>
                                <td class="">{{ $report_list->type                 }}</td>
                                <td class="">{{ $report_prop->default_permission    }}</td>
                                <td class="">{{ $report_list->not_use              }}</td>
                                <td class="">{{ $report_list->disabled             }}</td>
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
