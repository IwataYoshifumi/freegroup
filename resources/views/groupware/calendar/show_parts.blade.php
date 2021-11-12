@php

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;

//　表示用データの取得
//
$user = auth( 'user' )->user();
$access_list = $calendar->access_list();
$calprop = $calendar->calprop();

$route_access_list = route( 'groupware.access_list.show', [ 'access_list' => $access_list ] );

$permissions = Schedule::getPermissions();

if( $calendar->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $calendar->isWriter( $user->id )) {
    $authority = "スケジュール追加可能";
} elseif( $calendar->isReader( $user->id )) {
    $authority = "スケジュール閲覧のみ";
} else {
    $authority = "権限なし";
}

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row no-gutters">

    
    <label class="col-12 col-md-4 my_label text-md-right m-1">カレンダー名（管理者設定）</label>
    <div class="col-md-6 m-1">
        {{ $calendar->name }}
    </div>
    
    <label for="name" class="col-12 col-md-4 my_label text-md-right m-1">カレンダーアクセス権限</label>
    <div class="col-md-6">
        {{ $authority }}
    </div>
    


    <label class="col-12 col-md-4 my_label text-md-right m-1">アクセスリスト</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
        @can( 'view', $access_list )
            <a href='{{ $route_access_list }}' class='btn btn-sm btn-outline-secondary'>詳細</a>
        @endcan
    </div>

    <label class="col-12 col-md-4 my_label text-md-right m-1">カレンダー公開種別</label>
    <div class="col-md-6 m-1">
        {{ Calendar::getTypes()[$calendar->type] }}
    </div>
    
    <label class="col-12 col-md-4 my_label text-md-right m-1">スケジュール変更権限　初期値</label>
    <div class="col-md-6 m-1">
        {{ $permissions[$calendar->default_permission] }}
    </div>

    @if( $calendar->not_use )
        <label class="col-12 col-md-4 my_label text-md-right m-1">新規予定追加</label>
        <div class="col-md-6 m-1 alert-danger">
            否（不可）
        </div>
    @endif
    @if( $calendar->disabled ) 
        <label class="col-12 col-md-4 my_label text-md-right m-1">カレンダー無効化</label>
        <div class="col-md-6 m-1 alert-danger">
            無効化中<br>
            登録済み予定の変更不可（検索・表示は可）<br>
            Googleカレンダー同期停止済<br>
        </div>
    @elseif( 0 )
        <label class="col-12 col-md-4 my_label text-md-right m-1">カレンダー使用中</label>
        <div class="col-md-6 m-1">
            登録済み予定の変更可<br>
            Googleカレンダー同期可
        </div>
    @endif
    
    <label class="col-12 col-md-4 my_label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $calendar->memo }}
    </div>
    
</div>