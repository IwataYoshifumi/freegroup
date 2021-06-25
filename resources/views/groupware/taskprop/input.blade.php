@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;

use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$user = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//

$route_name = Route::currentRouteName();

$tasklist = $taskprop->tasklist;
$permissions = Task::getPermissions();

if( $tasklist->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $tasklist->isWriter( $user->id )) {
    $authority = "タスク追加可能";
} elseif( $tasklist->isReader( $user->id )) {
    $authority = "タスク閲覧のみ";
} else {
    $authority = "権限なし";
}

@endphp

@section('content')

@include( 'groupware.taskprop.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.taskprop.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    
                
                    <form method="POST" action="{{ url()->full() }}" enctype="multipart/form-data">
                        @csrf
                        <input type=hidden name='taskprop_id' value='{{ op( $taskprop )->id }}'>
                        <div class="form-group row">
                            <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">タスクリスト名</label>
                            <div class="col-md-6">
                                {{ $tasklist->name }}
                            </div>
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">タスクリストアクセス権限</label>
                            <div class="col-md-6">
                                {{ $authority }}
                            </div>
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">タスクリスト表示名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', $taskprop->name ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">メモ</label>
                            <div class="col-md-6">
                                {{ Form::text( 'memo', old( 'memo',  $taskprop->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>

                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">タスク変更権限　初期値</label>
                            <div class="col-md-6">
                                {{ Form::select( 'default_permission', $permissions, $taskprop->default_permission, [ 'class' => 'form-control' ] ) }}
                                
                                {{--
                                @foreach( $permissions as $permission => $value )
                                    @php
                                        $checked = ( $taskprop->default_permission == $permission ) ? 1 : 0;
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
                            {{ Form::color( 'background_color', $taskprop->background_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'color', 'onChange' => 'sample();' ] ) }}
                            
                            <div class="col-12"></div>
                            
                            <label for="text_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">文字色</label>
                            {{ Form::color( 'text_color', $taskprop->text_color, [ 'class' => 'col-12 col-md-1 form-control m-1', 'id' => 'text-color', 'onChange' => 'sample()' ] ) }}
                            <div class="col-12"></div>

                            @php
                                $checked = ( $taskprop->not_use ) ? 1 : 0;
                            @endphp
                            <div class="col-4 text-md-right m-1">タスク作成</div>
                            <div class="col-6">
                                <label for="not_use">新規にこのタスクを作成しない</label>
                                {{ Form::checkbox( 'not_use', 1, $checked, [ 'class' => 'checkboxradio m-1', 'id' => 'not_use' ] ) }}
                            </div>

                
                            @php
                                $checked = ( $taskprop->hide ) ? 1 : 0;
                            @endphp
                            <div class="col-4 text-md-right m-1">表示有無</div>
                            <div class="col-6">
                                <label for="hide">隠す</label>
                                {{ Form::checkbox( 'hide', 1, $checked, [ 'class' => 'checkboxradio m-1', 'id' => 'hide' ] ) }}
                            </div>



                            <div class="col-12"></div>

                
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

@if( $route_name == "groupware.taskprop.update" )
    @stack( 'javascript' )
@endif

@endsection
