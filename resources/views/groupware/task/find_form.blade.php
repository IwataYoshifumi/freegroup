@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;
use App\Http\Helpers\MyHelper;

$route_name = Route::currentRouteName();
$auth = auth( 'user' )->user();

$not_use  = [ 0 => '', 1 => 'タスク追加不可', -1 => 'タスク追加可能' ];
$disables = [ 0 => '', 1 => '無効タスクリスト', -1 => '有効タスクリスト' ];

$depts = Dept::getArrayforSelect();
$tasklist_auths = [ '' => '', 'owner' => 'タスクリスト管理権限あり', 'writer' => 'タスクリストへタスク追加可能', 'reader' => 'タスク閲覧可能' ];

$users = ( $request->users ) ? $request->users : [];

#if_debug( $tasklist_types );
if( is_debug() ) {
    $hidden_form_type = 'text';
} else {
    $hidden_form_type = 'hidden';
}

$depts = ( op( $request )->depts ) ? $request->depts : [];

$status = [ '完了' => '完了', '未完' => '未完', '' => '未完・完了とも' ];

$sorts = [ '' => '', 'created_at' => '作成日', 'user_id' => '作成者', 'tasklist_id' => 'タスクリスト', 'name' => '件名' ];

@endphp


{{ Form::open( [ 'route' => $route_name, 'id' => 'search_form' ] ) }}
    @csrf
    @method( 'GET' )


    <div class="widget">
        <fieldset class="border border-dark m-2">
            <div class="m-2">タスク検索条件</div>            
            <div class="container m-2 coltrolgroup">
                <div class="row">
                    
                    <div class="col-12">
                        @if( is_debug() ) @icon( debug )  search_query @endif
                        <input type='{{ $hidden_form_type }}' name='search_query' value=1>
                    </div><div class="col-12">
                        @if( is_debug() ) @icon( debug )  base_date @endif
                        <input type='{{ $hidden_form_type }}' name='base_date' value='{{ $request->base_date }}' id='base_date'>
                    </div>

                    @if( $request->tasklist_id ) 
                        @php
                            $taskprop =  TaskProp::where( 'tasklist_id', $request->tasklist_id )->where( 'user_id', user_id() )->first();
                        @endphp
                    
                    
                        <div class="col-11 border border-dark m-1 p-1" style="{{ op( $taskprop )->style() }}">
                            タスクリスト名：{{ op( $taskprop )->name }}
                        </div>
                        <input type=hidden name='tasklist_id' value='{{ $request->tasklist_id }}'>
                    @endif


                    @if( $route_name == 'groupware.task.index' )
    
                        {{-- タスクの期限検索 --}}

                        <div class="col-11 m-1 p-1 border border-dark">
                            <div class="m-2">タスク期限</div>
                            <x-input_date_span start="start_date" end="end_date" search-condition-for-span='task_span' />
                        </div>

                        {{-- タスクのステータス（完了・未完・保留） --}}

                        <div class="col-11 border border-dark m-1 p-1">
                            <span class="col-3">ステータス：</span>
                            @foreach( $status as $value => $text )
                                @php
                                    $id = 'status_' . $value;
                                    $checked = ( $request->status == $value ) ? true : false;
                                @endphp
                                <label for="{{ $id }}">{{ $text }}</label>
                                {{ Form::radio( 'status', $value, $checked, [ 'class' => 'checkboxradio', 'id' => $id ] ) }}
                            
                            @endforeach
                        </div>
                            
                        {{-- タスクリストの検索 --}}
                        <div class="col-11 border border-dark m-1 p-1">
                            <x-checkbox_tasklists name="tasklists" :values="$request->tasklists" />
                        </div>

                    @else
                        @if( is_debug() )
                            <div class="col-12">
                            @icon( debug ) {{ $request->start_date }}～{{ $request->end_date   }}
                            </div>
                        @endif
                    @endif

                    <div class="btn btn-outline bg-light" onClick="toggle_shain_search_form()">部署・社員検索・ペジネーション</div>
                    <script>
                        
                        $(document).ready( function() {
                            $('#shain_search_form').hide();
                            
                        });
                        
                        function toggle_shain_search_form() {
                            $('#shain_search_form').toggle( 'blind', { percent: 50 }, 200 );
                        }
                        
                    </script>

                    <div class="w-100 container" id="shain_search_form"><div class="row">
                        {{-- 部署検索 --}}
                        <fieldset class="col-4 border border-dark m-1 p-1">
                            <div class="m-2">部署検索</div>
                            {{-- Form::select( 'dept_id', $depts, $request->dept_id, [ 'class' => 'form-control' ] ) --}}
                            <x-checkboxes_depts :depts="op( $request )->depts" name="depts" button="部署検索する" />
                        </fieldset>
    
                        {{-- ユーザ検索 --}}
                        <fieldset class="col-4 border border-dark m-1 p-1">
                            <div class="m-2">社員検索</div>
                            <x-checkboxes_users :users="op( $request )->users" button="社員検索" />
    
                        </fieldset>
    
                        {{-- その他の検索条件 --}}
    
                        <fieldset class="col-3 border border-dark m-1 p-1">
                            <div class="m-2">検索対象</div>
                            @if( is_debug() or $route_name != "groupware.task.index" )
                                {{ Form::select( "search_condition", [ 'only_creator' => 'タスク作成者のみ', 'users' => 'タスク作成者＆関連社員' ], $request->search_condition,  [ 'class' => 'form-control w-80' ] ) }}
                            @endif
                            <div class="m-2">表示件数</div>
                                {{ Form::select( "pagination", config( 'config.paginations' ), $request->pagination,  [ 'class' => 'form-control w-80' ] ) }}
                        </fieldset>
    
                        {{-- タスクリスト種別 --}}
                        {{--
                        @if( ! $request->tasklist_id and 0 )
                            <div class="w-100">
                                <div class="btn btn-outline m-1 bg-light"
                                    data-toggle="collapse"
                                    data-target="#search_tasklists"
                                    aria-expand="true"
                                    aria-controls="search_tasklists">タスクリスト検索条件</div>
                                
                                
                                <div class="col-11 border border-dark m-1 p-1 collapse" id="search_tasklists">
        
                                    <div class="m-2">検索対象タスクリスト</div>
                                    
                                    @foreach( TaskList::getTypes() as $type => $name )
                                        @php 
                                            $checked = ( ! empty( op( $request->tasklist_types )[$type] )) ? 1 : 0;
                                        @endphp
                                        <label for="{{ $type }}">{{ $name }}</label>
                                        {{ Form::checkbox( "tasklist_types[$type]", $type,  $checked, [ 'id' => $type, "class" => "tasklist_types checkboxradio" ] ) }}
                                    @endforeach
            
                                    <div class="m-2">閲覧制限タスクリストのアクセス権限</div>
                                    {{ Form::select( "tasklist_auth", $tasklist_auths,  $request->tasklist_auth, [  "class" => "form-control col-4" ] ) }}
                                    
                                    <div class="m-2">その他の条件</div>
                                    <label for="show_hidden_tasklist">非表示タスクリストも検索</label>
                                    @php 
                                        $checked = ( $request->show_hidden_tasklist ) ? 1 : 0;
                                    @endphp
                                    {{ Form::checkbox( "show_hidden_tasklist", 1,  $checked, [ 'id' => 'show_hidden_tasklist', "class" => "checkboxradio m-1" ] ) }}
            
                                    @php 
                                        $checked = ( $request->show_retired_users ) ? 1 : 0;
                                    @endphp
                                    <label for="show_retired_users">退社済社員も検索</label>
                                    {{ Form::checkbox( "show_retired_users", 1,  $checked, [ 'id' => 'show_retired_users', "class" => "checkboxradio m-1" ] ) }}
                                </div>
                            </div>
                        @endif
                        --}}
                    </div></div>

                    {{-- ソート --}}
                    <div class="w-100">
                        <div class="btn btn-outline m-1 bg-light"
                            data-toggle="collapse"
                            data-target="#sorter"
                            aria-expand="true"
                            aria-controls="sorter">ソート</div>
                        
                        <div class="col-11 border border-dark m-1 p-1 collapse" id="sorter">
                            <div class="m-2">ソート順</div>
                            <div class="row container m-1">
                                @for( $i = 0; $i <= 2; $i++ )                             
                                    {{ Form::select( "sorts[$i]", $sorts, op( $request->sorts )[ $i ], [ 'class' => 'form-control col-3 m-1' ] ) }} 
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        function clear_button( class_name ) {
                            $check_clear = false;
                            $( class_name ).each( function() {
                                if( $(this).prop('checked') == true ) {
                                    $(this).prop('checked', false );
                                    $check_clear = true;
                                }
                            });
                            if( $check_clear ) { $('#search_form').submit(); }
                        }
                    </script>

                    <div class="col-11 m-1 row d-flex justify-content-between">
                        <button type="button" class="btn btn-search col-3 m-1" onClick="this.form.submit()">検索</button>
                        <div class="col-3"></div>
                        <button type="button" class="btn btn-search col-3 m-1" onClick="this.form.submit()">検索</button>
                    </div>
            </div>
            
            </div>
        </fieldset>
    </div>
{{ Form::close() }}
