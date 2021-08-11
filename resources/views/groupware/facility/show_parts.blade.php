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
$access_list = $facility->access_list();

$route_access_list = route( 'groupware.access_list.show', [ 'access_list' => $access_list ] );

if( $facility->isOwner( $user->id )) {
    $authority = "管理者";
} elseif( $facility->isWriter( $user->id )) {
    $authority = "設備予約　可能";
} elseif( $facility->isReader( $user->id )) {
    $authority = "設備予約状況の閲覧のみ";
} else {
    $authority = "権限なし";
}

$style = $facility->style();

$files = $facility->files;

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">設備名</label>
    <div class="col-md-6 m-1" style="{{ $style }}">
        {{ $facility->name }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">大分類</label>
    <div class="col-md-6 m-1">
        {{ $facility->category }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">小分類</label>
    <div class="col-md-6 m-1">
        {{ $facility->sub_category }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">管理番号</label>
    <div class="col-md-6 m-1">
        {{ $facility->control_number }}
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">保管場所</label>
    <div class="col-md-6 m-1">
        {{ $facility->location }}
    </div>

    
    <label for="name" class="col-md-4 col-form-label text-md-right m-1">設備アクセス権限</label>
    <div class="col-md-6">
        {{ $authority }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
        @can( 'view', $access_list )
            <a href='{{ $route_access_list }}' class='btn btn-sm btn-outline-secondary'>詳細</a>
        @endcan
    </div>

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">設備公開種別</label>
    <div class="col-md-6 m-1">
        {{ TaskList::getTypes()[$facility->type] }}
    </div>
    
    @if( $facility->disabled ) 
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">設備無効化</label>
        <div class="col-md-6 m-1 alert-danger">
            無効化中<br>
            登録済み設備予約の変更不可（検索・表示は可）<br>
        </div>
    @elseif( 0 )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">設備使用中</label>
        <div class="col-md-6 m-1">
            登録済み設備予約の変更可<br>
        </div>
    @endif

    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $facility->memo }}
    </div>
    
    <label  class="col-4 col-form-label text-md-right">添付ファイル</label>
    <div class="col-md-6 m-1">
        @foreach( $files as $file ) 
            <div class="col-12 text-truncate">
                @php
                    $route_file_download = route('groupware.file.download', [ 'file' => $file->id, 'class' => 'facility', 'model' => $facility->id ] );
                    $route_file_view     = route('groupware.file.view',     [ 'file' => $file->id, 'class' => 'facility', 'model' => $facility->id ] );
                    if( $user->can( 'view', $file )) {
                        $route_file_show     = route('groupware.file.show',  [ 'file' => $file->id ] ); 
                    } else {
                        $route_file_show = "";
                    }
                @endphp
                <a href="{{ $route_file_view     }}" class="btn btn_icon" target="_blank"> @icon( search ) </a>
                <a href="{{ $route_file_download }}" class="btn btn_icon" target="_blank"> @icon( file-download ) </a>
                <span class="uitooltip" title='{{ $file->file_name }} アップロード者：{{ $file->user->name }} アップロード日時：{{ $file->created_at }}'>{{ $file->file_name }}</span>
            </div>
        @endforeach
    </div>


</div>