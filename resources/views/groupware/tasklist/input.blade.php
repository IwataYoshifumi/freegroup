@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$user = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//
$access_lists = toArray( AccessList::whereOwner( $user->id )->get(), 'name', 'id' );

//　タスクリスト公開種別の選択肢 
//
$tasklist_types = array_merge( [''=>''], TaskList::getTypes() );

$permissions = Task::getPermissions();

if( ! $user->hasRole( 'CanCreateCompanyWideTaskLists' ) ) { unset( $tasklist_types['company-wide'] ); }
if( ! $user->hasRole( 'CanCreatePublicTaskLists'      ) ) { unset( $tasklist_types['public'] );       }

$route_name = Route::currentRouteName();

@endphp

@section('content')

@include( 'groupware.tasklist.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.tasklist.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                
                    <form method="POST" action="{{ url()->full() }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">タスクリスト名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', optional( $tasklist )->name ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  optional( $tasklist )->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
                            <div class="col-md-6">
                                {{ Form::select( 'access_list_id', $access_lists, old( 'access_list_id', $access_list->id ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">タスクリスト公開種別</label>
                            <div class="col-md-6">
                                {{ Form::select( 'type', $tasklist_types, old( 'type', $tasklist->type ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">タスク変更権限　初期値</label>
                            <div class="col-md-6">
                                {{ Form::select( 'default_permission', $permissions, $tasklist->default_permission, [ 'class' => 'form-control m-1' ] ) }}
                                @if( $route_name == 'groupware.tasklist.update' )
                                    <label for='init_users_default_permission' class='m-1'>各ユーザの変更権限　初期値を変更する</label>
                                    {{ Form::checkbox( 'init_users_default_permission', 1, 0, ['id' => 'init_users_default_permission' ] ) }}
                                @endif
                            </div>
                            
                            @if( $route_name == "groupware.tasklist.update" )
                                <label for="memo" class="col-md-4 col-form-label text-md-right m-1">新規タスク追加</label>
                                <div class="col-md-6">
                                    <label for="not_use">今後、新規のタスク作成はしない</label>
                                    {{ Form::checkbox( 'not_use', 1, old( 'not_use', $tasklist->not_use ),  [ 'class' => 'form-control m-1', 'id' => 'not_use' ] ) }}
                                </div>
                                
                                <label for="memo" class="col-md-4 col-form-label text-md-right m-1">無効化</label>
                                <div class="col-md-6">
                                    <label for="disabled">無効化する</label>
                                    {{ Form::checkbox( 'disabled', 1, old( 'disabled', $tasklist->disabled ),  [ 'class' => 'form-control m-1', 'id' => 'disabled' ] ) }}
                                </div>
                                
                                @if( old( 'disabled' )) 
                                    
                                    <label for="memo" class="col-md-4 col-form-label text-md-right m-1">タスクリスト無効化の再確認</label>
                                    <div class="col-md-6">
                                        <label for="comfirm-disabled_1">無効化後は、登録済みのタスクも変更できません。</label>
                                        {{ Form::checkbox( 'comfirm_disabled[0]', 1, false,  [ 'class' => 'form-control m-1', 'id' => 'comfirm-disabled_1' ] ) }}

                                        <!--
                                        <label for="comfirm-disabled_2">Googleタスクリスト同期設定も全て解除されます。</label>
                                        {{ Form::checkbox( 'comfirm_disabled[1]', 1, false,  [ 'class' => 'form-control m-1', 'id' => 'comfirm-disabled_2' ] ) }}
                                        -->
                                    </div>
                                
                                @endif
                                
                                @push( 'javascript' )
                                    <script>
                                        $( function() {
                                            $('#not_use').checkboxradio();
                                            $('#disabled').checkboxradio();
                                            $('#init_users_default_permission').checkboxradio();
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
@if( $route_name == "groupware.tasklist.update" )
    @stack( 'javascript' )
@endif

@endsection
