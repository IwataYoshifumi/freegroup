@php
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\MyHelpers;

$users = $group->users;
$access_list  = $group->access_list();

$url_to_the_access_list = route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ] );

// グループが参照されているアクセスリスト
//
$access_lists = AccessList::whereGroup( $group )->get();


#dump( $group, $group->access_list );

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">グループ名</label>
    <div class="col-md-6 m-1">
        {{ $group->name }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $group->memo }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">権限設定アクセスリスト</label>
    <div class="col-md-6 m-1">
        <a class="btn btn-outline-secondary m-1" href="{{ $url_to_the_access_list }}">{{ op( $access_list )->name }}</a>
    </div>
    
    <div class="col-md-11 m-1">
        <table class="table table-sm m-3 p-1">
            <tr><th colspan=3 class="bg-light">所属ユーザ</th></tr>
            <tr>
                <th>社員名</th>
                <th>部署名</th>
                <th>役職</th>
            </tr>
            @foreach( $users as $user )
                <tr>
                    <td>{{ $user->id }} : {{ $user->name }}</td>
                    <td>{{ $user->dept->name }}</td>
                    <td>{{ $user->grade }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    
    <div class="col-md-11 m-1">
        <table class="table table-sm m-3 p-1">
            <tr><th colspan=4 class="bg-light">利用先アクセスリスト</th></tr>
            <tr>
                <th>詳細・変更</th>
                <th>アクセスリスト名</th>
                <th>備考</th>
                <th>権限</th>
            </tr>
            @foreach( $access_lists as $access_list )
                @php
                    $href = route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ] );
                    $button = ( $access_list->isOwner( user_id() )) ? "詳細・変更" : "詳細" ;
                    
                @endphp
                <tr>
                    <td>{{ $access_list->id }} : <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">{{ $button }}</a></td>
                    <td>{{ $access_list->name }}</td>
                    <td>{{ $access_list->meemo }}</td>
                    <td></td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
