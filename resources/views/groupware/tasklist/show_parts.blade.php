@php

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;

//　表示用データの取得
//
$user = auth( 'user' )->user();
$access_list = $tasklist->access_list();
$taskprop = $tasklist->taskprop();

$route_access_list = route( 'groupware.access_list.show', [ 'access_list' => $access_list ] );

$permissions = Task::getPermissions();

if( $tasklist->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $tasklist->isWriter( $user->id )) {
    $authority = "スケジュール追加可能";
} elseif( $tasklist->isReader( $user->id )) {
    $authority = "スケジュール閲覧のみ";
} else {
    $authority = "権限なし";
}

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">タスクリスト名</label>
    <div class="col-md-6 m-1">
        {{ $tasklist->name }}
    </div>
    
    <label for="name" class="col-md-4 col-form-label text-md-right m-1">タスクリストアクセス権限</label>
    <div class="col-md-6">
        {{ $authority }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $tasklist->memo }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
        @can( 'view', $access_list )
            <a href='{{ $route_access_list }}' class='btn btn-sm btn-outline-secondary'>詳細</a>
        @endcan
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">タスクリスト公開種別</label>
    <div class="col-md-6 m-1">
        {{ TaskList::getTypes()[$tasklist->type] }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">タスク　変更権限初期値</label>
    <div class="col-md-6 m-1">
        {{ $permissions[$tasklist->default_permission] }}
    </div>

    @if( $tasklist->not_use )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">新規タスク追加</label>
        <div class="col-md-6 m-1 alert-danger">
            否（不可）
        </div>
    @endif
    @if( $tasklist->disabled ) 
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">タスクリスト無効化</label>
        <div class="col-md-6 m-1 alert-danger">
            無効化中<br>
            登録済みタスクの変更不可（検索・表示は可）<br>
        </div>
    @elseif( 0 )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">タスクリスト使用中</label>
        <div class="col-md-6 m-1">
            登録済みタスクの変更可<br>
        </div>
    @endif
</div>