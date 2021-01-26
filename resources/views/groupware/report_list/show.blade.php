@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;

use App\Http\Helpers\BackButton;

$report_prop = ReportProp::where( 'report_list_id', $report_list->id )->where( 'user_id', user_id() )->first();

$route_update_report_list = route( 'groupware.report_list.update', [ 'report_list' => $report_list ] );
$route_delete_report_list = route( 'groupware.report_list.delete', [ 'report_list' => $report_list ] );
$route_show_report_prop  = route( 'groupware.report_prop.show',  [ 'report_prop'  => $report_prop  ] );

if_debug( ReportProp::default_text_color(), ReportProp::default_background_color(), config( 'groupware.report_prop') );

$user = auth( 'user' )->user();

@endphp

@section('content')



<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.report_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    <div class="row">
                        @can( 'update', $report_list )
                            <a class="col-1 ml-2 btn icon_btn uitooltip" href="{{ $route_update_report_list }}" title="管理者設定変更"> @icon( edit )</a>
                        @endcan
                        @if( $report_list->canRead( user_id() ))
                            <a class="col-1 ml-2 btn icon_btn" href="{{ $route_show_report_prop }}" title="日報関連　個人設定"> @icon( user-cog )</a>
                        @endif   
                        @if( $user->can( 'delete', $report_list ))
                            <a class="col-1 col btn icon_btn text-danger ml-auto" href="{{ $route_delete_report_list }}" title="日報リスト削除"> @icon( trash-alt ) </a>
                        @endif
                    </div>

                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

                    <hr>
                    @include( 'groupware.report_list.show_parts' )
                        
                    {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
