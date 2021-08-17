@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$auth = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//
$access_lists = toArray( AccessList::whereOwner( $auth )->get(), 'name', 'id' );

//　日報リスト公開種別の選択肢 
//
$report_list_types = array_merge( [''=>''], ReportList::getTypes() );

$permissions = Report::getPermissions();

if( ! $auth->hasRole('CanCreatePrivateReportLists') ) { unset( $report_list_types['private'] );      }

$route_name = Route::currentRouteName();

@endphp

@section('content')

@include( 'groupware.report_list.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.report_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    
                    @if( $errors->count() )
                        <div class="alert-warning">
                            入力エラーで順序が元に戻っているます。確認してください。
                        </div>
                    @endif
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                
                    <form method="POST" action="{{ url()->full() }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">日報リスト名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', optional( $report_list )->name ), ['class' => 'form-control m-1', ] ) }}
                                @if( $route_name == 'groupware.report_list.update' )
                                    <label for='change_name_for_all_users' class='m-1'>各ユーザの表示名を変更する</label>
                                    {{ Form::checkbox( 'change_name_for_all_users', 1, 0, ['id' => 'change_name_for_all_users' ] ) }}
                                    <hr>
                                @endif
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  optional( $report_list )->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
                            <div class="col-md-6">
                                {{ Form::select( 'access_list_id', $access_lists, old( 'access_list_id', $access_list->id ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">日報リスト公開種別</label>
                            <div class="col-md-6">
                                {{ Form::select( 'type', $report_list_types, old( 'type', $report_list->type ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">日報変更権限　初期値</label>
                            <div class="col-md-6">
                                {{ Form::select( 'default_permission', $permissions, $report_list->default_permission, [ 'class' => 'form-control m-1' ] ) }}
                                @if( $route_name == 'groupware.report_list.update' )
                                    <label for='init_users_default_permission' class='m-1'>各ユーザの変更権限　初期値を変更する</label>
                                    {{ Form::checkbox( 'init_users_default_permission', 1, 0, ['id' => 'init_users_default_permission' ] ) }}
                                    <hr>
                                @endif
                            </div>
                            
                            @if( $route_name == "groupware.report_list.update" )
                                <label for="memo" class="col-md-4 col-form-label text-md-right m-1">新規日報追加</label>
                                <div class="col-md-6">
                                    <label for="not_use">今後、新規で日報を作成はしない</label>
                                    {{ Form::checkbox( 'not_use', 1, old( 'not_use', $report_list->not_use ),  [ 'class' => 'form-control m-1', 'id' => 'not_use' ] ) }}
                                </div>
                                
                                <label for="memo" class="col-md-4 col-form-label text-md-right m-1">無効化</label>
                                <div class="col-md-6">
                                    <label for="disabled">無効化する</label>
                                    {{ Form::checkbox( 'disabled', 1, old( 'disabled', $report_list->disabled ),  [ 'class' => 'form-control m-1', 'id' => 'disabled' ] ) }}
                                </div>
                                
                                @if( old( 'disabled' )) 
                                    <label for="memo" class="col-md-4 col-form-label text-md-right m-1">日報リスト　無効化の再確認</label>
                                    <div class="col-md-6">
                                        <label for="comfirm-disabled_1">無効化後は、登録済みの日報も変更・削除できません。</label>
                                        {{ Form::checkbox( 'comfirm_disabled[0]', 1, false,  [ 'class' => 'form-control m-1', 'id' => 'comfirm-disabled_1' ] ) }}

                                        <label for="comfirm-disabled_2">またこの日報リストを利用したいときは、無効化を解除することで再利用できます。</label>
                                        {{ Form::checkbox( 'comfirm_disabled[1]', 1, false,  [ 'class' => 'form-control m-1', 'id' => 'comfirm-disabled_2' ] ) }}
                                    </div>
                                @endif
                                
                                @push( 'javascript' )
                                    <script>
                                        $( function() {
                                            $('#not_use').checkboxradio();
                                            $('#disabled').checkboxradio();
                                            $('#init_users_default_permission').checkboxradio();
                                            $('#change_name_for_all_users').checkboxradio();
                                            $('#comfirm-disabled_1').checkboxradio();
                                            $('#comfirm-disabled_2').checkboxradio();
                                        });
                                    </script>
                                @endpush
                            @endif
                        </div>                        
                            
                        <div class="col-12"></div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">登録</button>
                                {{ BackButton::form() }}
                                </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@if( $route_name == "groupware.report_list.update" )
    @stack( 'javascript' )
@endif

@endsection
