@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Report;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$user = auth( 'user' )->user();
$report_list = $report_prop->report_list;

$route_show_report_list   = route( 'groupware.report_list.show', [ 'report_list' => $report_list ] );
$route_update_report_list = route( 'groupware.report_list.update', [ 'report_list' => $report_list ] );
$route_update_report_prop  = route( 'groupware.report_prop.update',  [ 'report_prop'  => $report_prop  ] );

$info = "<i class='fas fa-minus-circle' style='color:lightgray'></i>";
$permissions = Report::getPermissions();

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.report_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                        @can( 'update', $report_prop )
                            <a class="btn icon_btn" href="{{ $route_update_report_prop }}" title="個人設定変更"> @icon( edit ) </a>
                        @endcan
                        
                        @can( 'view', $report_list )
                                <a class="btn icon_btn" href="{{ $route_show_report_list }}" title="日報リスト管理情報"> @icon( config ) </a>
                        @endcan
                        
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト名{!! $info !!}   {{-- htmlspecialchars OK --}}
                            </label>
                            <div class="col-md-6 m-1">
                                {{ $report_list->name }}
                            </div>
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト表示名・色設定</label>
                            <div class="col-md-6 m-1" style='{{ $report_prop->style() }}'>
                                {{ $report_prop->name }}
                            </div>
                        
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト公開種別{!! $info !!}</label>   {{-- htmlspecialchars OK --}}
                            <div class="col-md-6 m-1">
                                {{ ReportList::getTypes()[$report_list->type] }}
                            </div>
                        
                            @if( $report_list->not_use )
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">【管理者設定】</label>
                                <div class="col-md-6 m-1"><span class="alert-danger p-2 text-dark">新規日報　追加不可</span></div>
                            @endif

                            @if( $report_list->disabled )
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">【管理者設定】</label>
                                <div class="col-md-6 m-1"><span class="alert-danger p-2 text-dark">既存日報　修正不可</span></div>
                            @endif
                            
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">【個人設定】作成日報変更権限（初期値）</label>
                            <div class="col-md-6 m-1">{{ $permissions[ $report_prop->default_permission ] }}</div>

                            @if( $report_prop->not_use and ! $report_list->not_use )
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">【個人設定】</label>
                                <div class="col-md-6 m-1"><span class="alert-warning p-2 text-dark">新規に日報を作成しまし</span></div>
                            @endif

                            @if( $report_prop->hide )
                                <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">【個人設定】</label>
                                <div class="col-md-6 m-1"><span class="alert-warning p-2 text-dark">日報を表示しない</span></div>
                            @endif
                            
                            

                            <div class="col-4 m-1"></div>
                            <ul class="col-7 m-1">
                                <ui>{!! $info !!}は日報リスト管理者設定</ui>   {{-- htmlspecialchars OK --}}
                            </ul>
                        </div>
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
