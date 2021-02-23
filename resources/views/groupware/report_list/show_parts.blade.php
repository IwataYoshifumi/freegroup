@php

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;

//　表示用データの取得
//
$auth = auth( 'user' )->user();
$access_list = $report_list->access_list();
$report_prop = $report_list->report_prop();

$route_access_list = route( 'groupware.access_list.show', [ 'access_list' => $access_list ] );

$permissions = Report::getPermissions();

if( $report_list->isOwner( $auth->id )) {
    $authority = "管理者";
} elseif( $report_list->isWriter( $auth->id )) {
    $authority = "スケジュール追加可能";
} elseif( $report_list->isReader( $auth->id )) {
    $authority = "スケジュール閲覧のみ";
} else {
    $authority = "権限なし";
}

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト名</label>
    <div class="col-md-6 m-1" style="{{ $report_prop->style() }}">
        {{ $report_list->name }}
    </div>
    
    <label for="name" class="col-md-4 col-form-label text-md-right m-1">日報リストアクセス権限</label>
    <div class="col-md-6">
        {{ $authority }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $report_list->memo }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
        @can( 'view', $access_list )
            <a href='{{ $route_access_list }}' class='btn btn-sm btn-outline-secondary'>詳細</a>
        @endcan
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報　リスト公開種別</label>
    <div class="col-md-6 m-1">
        {{ ReportList::getTypes()[$report_list->type] }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報　変更権限　初期値</label>
    <div class="col-md-6 m-1">
        {{ $permissions[$report_list->default_permission] }}
    </div>


    @if( $report_list->not_use )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">新規日報追加</label>
        <div class="col-md-6 m-1 alert-danger">
            否（不可）
        </div>
    @endif
    @if( $report_list->disabled ) 
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト無効化</label>
        <div class="col-md-6 m-1 alert-danger">
            無効化中<br>
            登録済み日報も変更不可（検索・表示は可）<br>
        </div>
    @elseif( 0 )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">日報リスト使用中</label>
        <div class="col-md-6 m-1">
            登録済み日報変更可能<br>
        </div>
    @endif


</div>


    <hr>
    
    