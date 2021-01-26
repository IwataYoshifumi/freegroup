@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Report;

use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$user = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//

$route_name = Route::currentRouteName();

$report_list = $report_prop->report_list;
$permissions = Report::getPermissions();

if( $report_list->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $report_list->isWriter( $user->id )) {
    $authority = "日報追加可能";
} elseif( $report_list->isReader( $user->id )) {
    $authority = "日報閲覧のみ";
} else {
    $authority = "権限なし";
}

@endphp

@section('content')

@include( 'groupware.report_prop.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.report_prop.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                
                    <form method="POST" action="{{ url()->full() }}" enctype="multipart/form-data">
                        @csrf
                        <input type=hidden name='report_prop_id' value='{{ op( $report_prop )->id }}'>
                        <div class="form-group row">
                            <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">日報リスト名</label>
                            <div class="col-md-6">
                                {{ $report_list->name }}
                            </div>
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">日報リストアクセス権限</label>
                            <div class="col-md-6">
                                {{ $authority }}
                            </div>
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">日報リスト表示名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', $report_prop->name ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">メモ</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  $report_prop->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>

                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">日報変更権限　初期値</label>
                            <div class="col-md-6">
                                {{ Form::select( 'default_permission', $permissions, $report_prop->default_permission, [ 'class' => 'form-control' ] ) }}
                                
                                {{--
                                @foreach( $permissions as $permission => $value )
                                    @php
                                        $checked = ( $report_prop->default_permission == $permission ) ? 1 : 0;
                                    @endphp
                                    <label for="{{ $permission }}">{{ $value }}</label>
                                    {{ Form::radio( 'default_permission', $permission, $checked, [ 'class' => 'permission_radio', 'id' => $permission ] ) }}<br>
                                @endforeach
                                --}}
                            </div>

                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1  d-none d-md-block">色サンプル</label>
                            <div class="col-md-6">
                                <span id="sample1" class="col-12 col-md-3 m-1 p-1">色サンプル</span>
                            </div>

                            <label for="backgroud_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">背景色</label>
                            {{ Form::color( 'background_color', $report_prop->background_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'color', 'onChange' => 'sample();' ] ) }}
                            
                            <div class="col-12"></div>
                            
                            <label for="text_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">文字色</label>
                            {{ Form::color( 'text_color', $report_prop->text_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'text-color', 'onChange' => 'sample()' ] ) }}
                
                            <div class="col-12"></div>
                            
                        
                        <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">登録</button>
                                {{ BackButton::form() }}
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>

@if( $route_name == "groupware.report_prop.update" )
    @stack( 'javascript' )
@endif

@endsection
