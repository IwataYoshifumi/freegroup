@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$user = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//

$route_name = Route::currentRouteName();

$calendar = $calprop->calendar;
$permissions = Schedule::getPermissions();
$google_private_key_file = $calprop->google_private_key_file();

$sync_levels = config( 'groupware.calprop.sync_level' );
#$sync_spans  = [ '60' => '前後３か月', '180' => '前後半年', '365' => '前後１年間', ];
$sync_spans  = config( 'groupware.calprop.sync_spans' );

if( $calendar->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $calendar->isWriter( $user->id )) {
    $authority = "スケジュール追加可能";
} elseif( $calendar->isReader( $user->id )) {
    $authority = "スケジュール閲覧のみ";
} else {
    $authority = "権限なし";
}

$sync_bidrection_or_not = [ 0 => '片方向', 1 => '両方向同期' ];

@endphp

@section('content')

@include( 'groupware.calprop.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.calprop.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    

                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                
                    <form method="POST" action="{{ url()->full() }}" enctype="multipart/form-data">
                        @csrf
                        <input type=hidden name='calprop_id' value='{{ op( $calprop )->id }}'>
                        <div class="form-group row">
                            <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">カレンダー名</label>
                            <div class="col-md-6">
                                {{ $calendar->name }}
                            </div>
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">カレンダーアクセス権限</label>
                            <div class="col-md-6">
                                {{ $authority }}
                            </div>
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">カレンダー表示名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', $calprop->name ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">メモ</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  $calprop->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>

                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">スケジュール変更権限　初期値</label>
                            <div class="col-md-6">
                                {{ Form::select( 'default_permission', $permissions, $calprop->default_permission, [ 'class' => 'form-control' ] ) }}
                                
                                {{--
                                @foreach( $permissions as $permission => $value )
                                    @php
                                        $checked = ( $calprop->default_permission == $permission ) ? 1 : 0;
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
                            {{ Form::color( 'background_color', $calprop->background_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'color', 'onChange' => 'sample();' ] ) }}
                            
                            <div class="col-12"></div>
                            
                            <label for="text_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">文字色</label>
                            {{ Form::color( 'text_color', $calprop->text_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'text-color', 'onChange' => 'sample()' ] ) }}
                
                            <div class="col-12"></div>

                            @if( $route_name == "groupware.calprop.update" )                            
                                <div class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">予定作成</div>
                                <label for="not_use">新規に予定作成しない</label>
                                {{ Form::checkbox( 'not_use', 1, $calprop->not_use, [ 'id' => 'not_use', 'class' => 'checkboxradio' ] ) }}
                                <div class="col-12"></div>
                            
                                <div class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">表示有無</div>
                                <label for='hide'>隠す</label>
                                {{ Form::checkbox( 'hide', 1, $calprop->hide, [ 'id' => 'hide', 'class' => 'checkboxradio' ] ) }}
                                
                                <div class="col-12"></div>                            
                            @endif

                            <div class="col-4 m-1"></div>
                            <div class="col-6 m-1">
                                <a class="btn btn-outline-dark col-5 m-1"
                                    data-toggle="collapse"
                                    role="button"
                                    href="#google_sync_config"
                                    aria-expand="false"
                                    aria-controls="google_sync_config">Google同期設定</a>
                            </div>
                            <div class="col-12"></div>
                            
                            <div class='collapse col-12' id="google_sync_config">
                                <div class="row">
                                    @if( $calprop->google_sync_check ) 
                                        <div class="col-4 m-1 text-right">Googleカレンダー同期ＯＮ</div>
                                        <label for='google_sync_on'>Googleカレンダー同期</label>                        
                                        {{ Form::checkbox( 'google_sync_on', 1, $calprop->google_sync_on, [ 'id' => 'google_sync_on', 'class' => 'col-md-3' ] ) }}
                                    @endif
        
                                    <div class="col-12"></div>
        
                                    <div class="col-4 m-1 text-right">同期レベル</div>
                                    {{ Form::select( 'google_sync_level', $sync_levels, $calprop->google_sync_level,  [ 'class' => 'col-5 form-control m-1' ] ) }}
        
                                    <div class="col-12"></div>
                                    
                                    <div class="col-4 m-1 text-right">同期期間</div>
                                    {{ Form::select( 'google_sync_span', $sync_spans, $calprop->google_sync_span,  [ 'class' => 'col-3 form-control m-1' ] ) }}
        
                                    <div class="col-12"></div>
        
                                    <div class="col-4 m-1 text-right">同期双方向</div>
        
                                    {{ Form::select( 'google_sync_bidirectional', $sync_bidrection_or_not, $calprop->google_sync_bidirectional,  [ 'class' => 'col-3 form-control m-1' ] ) }}
        
        
        
                                    <div class="col-12"></div>
        
        
                                    <div class="col-4 m-1 text-right">Google カレンダーID</div>
                                    {{ Form::text( 'google_calendar_id', $calprop->google_calendar_id, [ 'class' => 'col-7 form-control m-1' ] ) }}
        
                                    <div class="col-12"></div>
                                    
                                    <div class="col-4 m-1 text-right">Google サービスアカウントID</div>
                                    {{ Form::email( 'google_id', $calprop->google_id, [ 'class' => 'col-7 form-control m-1' ] ) }}
        
                                    <div class="col-12"></div>
                                
                                    <div class="col-4 m-1 text-right">サービスアカウント秘密鍵</div>
                                    {{ Form::file( 'google_private_key_file', [ 'class' => 'col-7 m-1' ] ) }}
        
                                    <div class="col-4 m-1"></div>
                                    <div class="col-6 m-1">
                                        {{ op( $google_private_key_file )->file_name }} 
                                    </div>
                                    
                                    <div class="col-12"></div>
                                
                                </div>
                            </div>
                        
                        <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">登録</button>
                                {{ BackButton::form() }}
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>

@if( $route_name == "groupware.calprop.update" )
    @stack( 'javascript' )
@endif

@endsection
