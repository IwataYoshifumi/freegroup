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
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">カレンダー名</label>
    <div class="col-md-6 m-1" style="{{ $calprop->style() }}">
        {{ $calendar->name }}
    </div>
    
    <label for="name" class="col-md-4 col-form-label text-md-right m-1">カレンダーアクセス権限</label>
    <div class="col-md-6">
        {{ $authority }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $calendar->memo }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
        @can( 'view', $access_list )
            <a href='{{ $route_access_list }}' class='btn btn-sm btn-outline-secondary'>詳細</a>
        @endcan
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">カレンダー公開種別</label>
    <div class="col-md-6 m-1">
        {{ Calendar::getTypes()[$calendar->type] }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">スケジュール変更権限　初期値</label>
    <div class="col-md-6 m-1">
        {{ $permissions[$calendar->default_permission] }}
    </div>


    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">今後は使用しない</label>
    <div class="col-md-6 m-1">
        @if( $calendar->not_use ) カレンダー使用停止中 @endif
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">検索対象外・Google同期解除</label>
    <div class="col-md-6 m-1">
        @if( $calendar->disabled ) 検索対象外・Google同期解除済み @endif
    </div>
</div>


    <hr>
    
    