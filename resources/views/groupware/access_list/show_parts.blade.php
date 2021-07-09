@php

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;

//　表示用データの取得
//
$acls = ACL::with([ 'aclable' ])->where( 'access_list_id', $access_list->id )->orderBy( 'order' )->get();
$roles = ACL::get_array_roles_for_select();
$role_list = new ListOfUsersInTheAccessList( $access_list );

$owners  = $role_list->getOwners();
$writers = $role_list->getWriters();
$readers = $role_list->getReaders();
$freeBusyReaders = $role_list->getFreeBusyReaders();

$accesslistables = $access_list->accesslistables();

$class_names = [ Group::class => "グループ",
                 User::class  => "社員",
                 Dept::class  => "部署",
                 MyFile::class => "ファイル",
                 Calendar::class => "カレンダー",
                 ReportList::class => "日報リスト",
                 TaskList::class => "タスクリスト",
                ]

@endphp

<div class="col-12 m-1"></div>
<div class="form-group row">
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">アクセスリスト名</label>
    <div class="col-md-6 m-1">
        {{ $access_list->name }}
    </div>
    
    <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">備考</label>
    <div class="col-md-6 m-1">
        {{ $access_list->memo }}
    </div>
    
    @if( $access_list->default )
        <label for="dept_id" class="col-md-4 col-form-label text-md-right m-1">デフォルト</label>
        <div class="col-md-6 m-1">
            <b>レ</b>
            <span class="alert-warning m-1">初期設定値は削除できません。</span>
        </div>
    @endif

    <hr>

    <table class="table table-border col-11 m-3 p-1">
        <tr>
            <th>順序</th>
            <th>権限</th>
            <th>種別</th>
            <th colspan=2>社員・部署・グループ</th>
        </tr>
        @foreach( $acls as $acl )
            <tr>
                <td>{{ $acl->order            }}</td>
                <td>{{ $roles[ $acl->role ]   }}</td>
                <td>{{ $acl->p_type()         }}</td>
                <td>{{ $acl->p_aclable_name() }}</td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ $acl->aclable_url() }}">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
    
    <div class="col-12"></div>
    <a class="btn btn-outline-secondary m-3 p-1 runEffect" data-target="user_role_list">ユーザの権限リストを表示</a>
    <table class="table table-border table-sm col-11 m-3 p-1" id="user_role_list">
        @foreach( $roles as $role => $role_name )
            @php
                if( ! $role ) { continue; }
                $method = "get".ucfirst( $role )."s";
                #dump( $method );
            @endphp
        
            @foreach( $role_list->$method() as $user ) 
                @if( $loop->first )
                    <tr><th colspan=4 class="bg-light">{{ $role_name }}</th></tr>
                @endif
                <tr>
                    <td>&nbsp;</td>
                    <td>{{ optional( $user->dept )->name }}</td>
                    <td>{{ $user->grade }}</td>
                    <td>{{ $user->name  }}</td>
                    </tr>
                </tr>
            @endforeach
        @endforeach
    </table>    
    
    <div class="col-12"></div>
    <a class="btn btn-outline-secondary m-3 p-1 runEffect" data-target="model_list">アクセスリスト設定先を表示</a>

    <table class="table table-border table-sm col-11 m-3 p-1" id="model_list">
        @foreach( $accesslistables as $i => $model )

            @if( $loop->first )
                <tr><th colspan=4 class="bg-light">アクセスリスト設定先</th></tr>
                <tr>
                    <th>詳細</th>
                    <th>クラス名</th>
                    <th>名前</th>
                </tr>
            @endif

            @php
                // if_debug( $model );
                if( is_null( $model )) { continue; }
                
                if( $model instanceof Group ) {
                    $href = route( 'groupware.group.show', ['group' => $model->id ] );
                } elseif( $model instanceof Calendar ) {
                    $href = route( 'groupware.calendar.show', ['calendar' => $model->id ] );
                } elseif( $model instanceof ReportList ) {
                    $href = route( 'groupware.report_list.show', [ 'report_list' => $model->id ] );
                } elseif( $model instanceof TaskList ) {
                    $href = route( 'groupware.tasklist.show', [ 'tasklist' => $model->id ] );
                }
                $class_name = ( op( $class_names )[get_class( $model )] ) ? $class_names[get_class( $model )] : get_class( $model );
                    
            @endphp

            <tr>
                <td>
                    <a href="{{ $href }}" class="btn btn-sm btn-outline-secondary">詳細</a>
                    @if( is_debug() )  {{ $model->id }} @endif
                </td>
                <td>{{ $class_name }}</td>
                <td>{{ $model->name }}</td>
                </tr>
            </tr>
        @endforeach
    </table>    
    
    <script>
        $('.runEffect').click( function() {
            var effect = "blind";
            var options = {};
            var target = $(this).data('target');
            $('#'+target).toggle( effect, options, 500 );
            
        })
    
        $(document).ready( function() {
            $("#user_role_list").hide();
            $("#model_list").hide();
        });
        
    </script>

    
    