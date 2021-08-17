@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;

#if_debug( request()->url(), request()->fullurl(), request()->path(), request()->all() );


$not_use  = [ 0 => '', 1 => 'スケジュール追加不可', -1 => 'スケジュール追加可能' ];
$disables = [ 0 => '', 1 => '無効カレンダー', -1 => '有効カレンダー' ];

$depts = Dept::getArrayforSelect();
$calendar_auths = [ '' => '', 'owner' => 'カレンダー管理権限あり', 'writer' => 'カレンダーへ予定追加可能', 'reader' => '予定閲覧可能' ];

$users = ( $request->users ) ? $request->users : [];

#if_debug( $calendar_types );
if( is_debug() ) {
    $hidden_form_type = 'text';
} else {
    $hidden_form_type = 'hidden';
}

$depts = ( op( $request )->depts ) ? $request->depts : [];


@endphp


{{ Form::open( [ 'url' => route( 'groupware.show_all.indexEexecSearch' ), 'id' => 'search_form' ] ) }}
    @csrf
    @method( 'GET' )


    <div class="widget">
        <fieldset class="border border-dark m-2">
            <div class="container m-2 coltrolgroup">
                <div class="row">

                    {{-- 
                      --
                      --
                      --    検索期間
                      --
                      --
                      --}}
                        <div class="col-11 m-1 p-1 border border-dark">
                            <div class="m-2">検索期間</div>
                            @php
                                $start = "start_date";
                                $end   = "end_date";
                            @endphp
                            <x-input_date_span :start="$start" :end="$end" />
                        </div>

                        <div class="col-11 m-1 p-1 border border-dark">
                            <div class="row ">
                                <div class="col-11 col-md-2 m-1 m-md-2">キーワード検索：</div>
                                {{ Form::text( 'keyword', $request->keyword, [ 'class' => 'col-10 col-md-5 ml-3 ml-md form-control' , 'title' => "キーワードは、件名と備考を検索します。"] ) }}
                            </div>
                        </div>
                    </div>

                    {{-- 
                      --
                      --
                      --    カレンダー
                      --
                      --
                      --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 m-1 p-2">
                                <div class="btn btn-sm btn-outline-dark openers" data-target="calendars_form">カレンダー</div>
                            </div>
                            
                            <fieldset class="col-12 col-md-4 border border-dark m-md-1 p-md-1 calendars_form">
                                <x-calendar_checkboxes :calendars="op( $request )->calendars" name="calendars" button="カレンダー検索" />
                            </fieldset>
                        </div>
                    </div>

                    {{-- 
                      --
                      --
                      --    タスクリスト
                      --
                      --
                      --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 m-1 p-2">
                                <div class="btn btn-sm btn-outline-dark openers" data-target="tasklists_form">タスクリスト</div>
                            </div>
                            <div class="col-12 m-md-1 p-md-1 tasklists_form">                            
                                <fieldset class="col-12 col-md-4 border border-dark m-md-1 p-md-1">
                                    @php
                                    $array_task_status = [ '完了' => '完了', '未完' => '未完', '未完・完了とも' => '' ];
                                    $i = 1;
                                    @endphp
                                    @foreach( $array_task_status as $key => $value )
                                        @php
                                        $id = "task_status_" . $i;
                                        $i++;
                                        $checked = ( $request->task_status == $value ) ? 1 : 0;
                                        @endphp
                                        <label for="{{ $id }}">{{ $key }}</label>
                                        {{ Form::radio( 'task_status', $value, $checked, [ 'id' => $id, 'class' => 'checkboxradio' ] ) }}
                                    @endforeach
                                </fieldset>
                                
                                <fieldset class="col-12 col-md-4 border border-dark m-md-1 p-md-1 tasklists_form">
                                    <x-tasklist_checkboxes :tasklists="op( $request )->tasklists" name="tasklists" button="タスクリスト検索" />
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    {{-- 
                      --
                      --
                      --    日報リスト
                      --
                      --
                      --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 m-1 p-2">
                                <div class="btn btn-sm btn-outline-dark openers" data-target="report_lists_form">日報リスト</div>
                            </div>
                            
                            <fieldset class="col-12 col-md-4 border border-dark m-1 p-1 report_lists_form">
                                <x-report_list_checkboxes :reportLists="op( $request )->report_lists" name="report_lists" button="日報リスト検索検索" />
                            </fieldset>
                        </div>
                    </div>
                    
                    {{-- 
                      --
                      --
                      --    顧客・部署・社員
                      --
                      --
                      --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 m-1 p-2">
                                <div class="btn btn-sm btn-outline-dark openers" data-target="customers_users_form">社員・部署・顧客</div>
                            </div>
                            
                            <div class="col-11 col-md-6 border border-dark d-flex flex-wrap m-1 p-1">

                                {{-- 社員検索 --}}
                                <fieldset class="w-50 customers_users_form">
                                    <x-checkboxes_users :users="op( $request )->users" button="社員" />
                                </fieldset>
            
                                {{-- 部署検索 --}}
                                <fieldset class="w-50 customers_users_form">
                                    <x-checkboxes_depts :depts="op( $request )->depts" name="depts" button="部署" />
                                </fieldset>
                                
                                {{--　関連社員も検索 --}}

                                <div class="w-100 mt-2">                                
                                    <label for="search_users">関連社員も検索</label>
                                    {{ Form::checkbox( 'search_users', 1, $request->search_users, [ 'id' => 'search_users', 'class' => 'checkboxradio' ] ) }}
                                </div>
                            </div>

                            {{-- 顧客検索 --}}
                            <fieldset class="col-11 col-md-3 border border-dark m-1 p-1 customers_users_form">
                                <!--<div class="m-2">顧客検索</div>-->
                                <x-checkboxes_customers :customers="op( $request )->customers" name="customers" button="顧客" />
                            </fieldset>


                        </div>
                    </div>
                    
                    {{-- 
                      --
                      --
                      --    その他検索条件・ペジネーション・ソート
                      --
                      --
                      --}}
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 m-1 p-2">
                                <div class="btn btn-sm btn-outline-dark openers" data-target="other_condition_form">その他の検索条件・ソート</div>
                            </div>
                            {{-- 部署検索 --}}
                            <fieldset class="col-11 border border-dark m-1 p-1 other_condition_form">
                                <div class="row">
                                    <div class="col-12 col-md-2">
                                        {{--
                                          --
                                          --
                                          -- ペジネーション
                                          --
                                          --
                                          --}}
                                        <div class="m-2">ペジネーション</div>
                                        {{ Form::select( 'pagination', config( 'constant.pagination' ), $request->pagination, [ 'class' => 'form-control' ] ) }}
                                    </div>
                                    <div class="col-8">
                                        {{--
                                          --
                                          --
                                          -- ソート
                                          --
                                          --
                                          --}}
                                        @php
                                        $array_sort = [ '' => '', 'time' => '日時', 'type' => 'タイプ', 'user_id' => '社員名' ];
                                        $array_desc = [ 'asc' => '昇順', 'desc' => '降順' ];
                                        @endphp
                                        <div class=" m-2">ソート</div>
                                            <div class="row">
                                            @for( $i = 0; $i <= 2; $i++ )
                                                <div class="col-12 col-md-4">
                                                    {{ Form::select( "order_by[$i]", $array_sort, op( $request->order_by )[$i], [ 'class' => 'form-control' ] ) }}
                                                    {{ Form::select( "asc_desc[$i]", $array_desc, op( $request->asc_desc )[$i], [ 'class' => 'form-control' ] ) }}
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
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


                    <div class="col-11 m-1 row d-flex justify-content-start">
                            <button type="button" class="btn btn-search m-1 col-3" onClick="this.form.submit()">検索</button>
                            <div class="m-1 bg-info">{{ BackButton::form() }}</div>
                    </div>
            </div>
            
            </div>
        </fieldset>
        <script>

            $(document).ready( function() { 
            
                @if( empty( $request->users ) and empty( $request->depts ) and empty( $request->customers ))
                    $(".customers_users_form").hide(); 
                @endif
                $(".other_condition_form").hide();
                @if( ! isset( $request->writable_calender ) and ! ( is_array( $request->calendars ) and count( $request->calendars )) ) 
                    $(".calendars_form").hide();
                @endif
                @if( ! isset( $request->writable_tasklist ) and ! ( is_array( $request->tasklists ) and count( $request->tasklists ))) 
                    $(".tasklists_form").hide();
                @endif
                @if( ! isset( $request->writable_report_list )  and ! ( is_array( $request->report_lists ) and count( $request->report_lists ))) 
                    $(".report_lists_form").hide();
                @endif
            });

            //　フォームのトグル表示ボタン
            //
            $('.openers').on( 'click', function() {
                var target = $(this).data( 'target' );
                $("." + target ).toggle( 'blind', { percent:50 }, 100 );
            });
        </script>

        
    </div>
{{ Form::close() }}
