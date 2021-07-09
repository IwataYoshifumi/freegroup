@extends('layouts.app')

@php

use Illuminate\Support\Arr;
use Carbon\Carbon;

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\Http\Helpers\BackButton;

$array_roles = ACL::get_array_roles_for_select();
$array_roles[''] = '-';

$auth = auth( 'user' )->user();
$report_list_types   = ReportList::getTypes();
$default_permissions = ReportProp::getPermissions();

$today = Carbon::now();

$options = [ 'start_date' => Carbon::parse( 'first day of January' )->format( 'Y-m-d' ), 
             'end_date'   => Carbon::parse( 'last day of December' )->format( 'Y-m-d' ), 
             'users'      => [ user_id() ] 
             ];

#dump( $find );

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            @include( 'groupware.report_list.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.report_list.search_form' )
                    
                    <table class="table table-striped m-1 p-1 border clearfix">
                        <tr class="">
                            <th class="">アクション</th>
                            <th class="">日報リスト名</th>
                            <th class="">公開種別<span title="管理者設定">@icon( info-circle )</span></th>
                            <th class="">アクセスリスト<br>設定権限<span title="管理者設定">@icon( info-circle )</span></th>
                            <th class="">デフォルト<br>編集権限設定</th>
                            <th class="">日報追加</th>
                            @if( $request->show_disabled )
                                <th class="">無効化<span title="管理者設定">@icon( info-circle )</span></th>
                            @endif
                            @if( $request->show_hidden ) 
                                <th class="">非表示</th>
                            @endif
                            
                            
                        </tr>
                        @php
                            $class_new_report       = 'btn btn-sm btn-success';
                            $class_show_report_prop = 'btn btn-sm btn-outline btn-outline-secondary';
                            $class_show_report_list = 'btn btn-sm btn-outline btn-outline-secondary';
                        
                        @endphp
                        @foreach( $report_lists as $i => $report_list )
                            @php
                            
                                #$href = route( 'groupware.report_list.show', [ 'report_list' => $report_list->id ] );

                                $report_prop = $report_list->my_report_prop();

                                $route_new_report  = route( 'groupware.report.create', [ 'report_list' => $report_list->id ] );
                                $route_show_report_prop  = route( 'groupware.report_prop.show',    [ 'report_prop'  => $report_prop->id ] );
                                $route_show_report_list = route( 'groupware.report_list.show',   [ 'report_list' => $report_list->id ] );
                                #$route_to_index_reports = route( 'groupware.report.index', [ 'report_list_id' => $report_list->id ] );
                                
                                
                                $options['report_lists'] = [ $report_list->id ];
                                $route_to_index_reports = route( 'groupware.show_all.indexEexecSearch', $options );

                                //$report_prop = $report_list->report_prop();

                                # $style = "color: ". $report_prop->text_color . "; background-color:" . $report_prop->background_color . ";";
                                $style = $report_prop->style();
                                
                                if( $report_list->isOwner( $auth->id )) {
                                    $authority = "管理者";
                                } elseif( $report_list->isWriter( $auth->id )) {
                                    $authority = "日報追加可能";
                                } elseif( $report_list->isReader( $auth->id )) {
                                    $authority = "閲覧のみ可";
                                } else {
                                    $authority = "権限なし";
                                }
                                $button = ( $report_list->canRead( $auth->id )) ? "詳細・変更" : "詳細";
                                
                                if( $report_list->name == $report_prop->name ) {
                                    $name = htmlspecialchars( $report_prop->name );
                                } else {
                                    $name  = htmlspecialchars( $report_prop->name ) . "<span title='管理者設定：";
                                    #$name  = $report_prop->name . "<span title='管理者設定：";
                                    $name .= htmlspecialchars( $report_list->name ) . "'><i class='fas fa-info-circle m-1'></i></span>";
                                }
                                
                                $type       = $report_list_types[$report_list->type];
                                $permission = op( $default_permissions )[ $report_prop->default_permission ];
                                #dd( $default_permissions, $permission, $report_prop->default_permission );
                                $disabled = ( $report_list->disabled ) ? "無効中" : "";
                                $hidden   = ( $report_prop->hide     ) ? "非表示設定" : "";
                                
                                if( $report_list->not_use ) { 
                                    $not_use = "追加不可<i title='管理者設定' class='fas fa-info-circle m-1'></i>";
                                } elseif( $report_prop->not_use ) {
                                    $not_use = "追加不可";
                                } else {
                                    $not_use = "";
                                }
                                
                            @endphp
                        
                            <tr class="">
                                <td class="">
                                    @if( $report_list->canRead( user_id() ) )
                                        <a class="{{ $class_show_report_prop }}" href="{{ $route_to_index_reports }}">日報一覧</a>
                                    
                                        <a class="{{ $class_show_report_prop }}" href="{{ $route_show_report_prop  }}">表示設定</a>
                                    @endif
                                    @if( $report_list->isOwner( user_id() ) )
                                        <a class="{{ $class_show_report_list }}" href="{{ $route_show_report_list }}">管理者設定</a>
                                    @endif

                                    @if( is_debug() ) 
                                        <span class="uitooltip icon_debug m-1" title='report_list_id {{ $report_list->id }} report_prop_id {{ $report_prop->id }}'>
                                            <i class="fab fa-deploydog"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="">
                                    <span style="{{ $style }}" class="border border-round m-1 p-2">{!! $name !!}</span>   {{-- htmlspecialchars OK --}}
                                </td>
                                <td class="">{{ $type               }}</td>
                                <td class="">{{ $authority          }}</td>
                                <td class="">{{ $permission         }}</td>
                                <td class="">{!! $not_use          !!}</td>  {{-- htmlspecialchars OK --}}
                                
                                @if( $request->show_disabled )
                                    <td class="">{{ $disabled }}</td>
                                @endif
                                @if( $request->show_hidden )
                                    <td>{{ $hidden }}</td>
                                @endif
                                
                                
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
