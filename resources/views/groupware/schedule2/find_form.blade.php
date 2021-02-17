@php


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

$route_name = Route::currentRouteName();

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


{{ Form::open( [ 'route' => $route_name, 'id' => 'search_form' ] ) }}
    @csrf
    @method( 'GET' )


    <div class="widget">
        <fieldset class="border border-dark m-2">
            <div class="m-2">カレンダー検索条件</div>            
            <div class="container m-2 coltrolgroup">
                <div class="row">
                    <div class="col-12">
                        @if( is_debug() ) @icon( debug )  search_query @endif
                        <input type='{{ $hidden_form_type }}' name='search_query' value=1>
                    </div><div class="col-12">
                        @if( is_debug() ) @icon( debug )  base_date @endif
                        <input type='{{ $hidden_form_type }}' name='base_date' value='{{ $request->base_date }}' id='base_date'>
                    </div><div class="col-12">
                        {{-- 検索期間 --}}
                        @if( $route_name == 'groupware.schedule.index' )
                            <div class="col-11 m-1 p-1 border border-dark">
                                <div class="m-2">検索期間</div>
                                @php
                                    $start = "start_date";
                                    $end   = "end_date";
                                @endphp
                                <x-input_date_span :start="$start" :end="$end" />
                            </div>
                        @else
                            @if( is_debug() )
                                <div class="col-12">
                                @icon( debug ) {{ $request->start_date }}～{{ $request->end_date   }}
                                </div>
                            @endif
                        @endif
                    </div>

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
                        @if( is_debug() or $route_name != "groupware.schedule.index" )
                            {{ Form::select( "search_condition", [ 'only_creator' => '予定作成者のみ', 'users' => '予定作成者＆関連社員' ], $request->search_condition,  [ 'class' => 'form-control w-80' ] ) }}
                        @endif
                        <div class="m-2">表示対象</div>
                        @if( is_debug() or $route_name != "groupware.schedule.index" )
                            {{ Form::select( "display_axis", [ 'only_creator' => '予定作成者のみ', 'users' => '予定作成者・関連社員' ], $request->display_axis,  [ 'class' => 'form-control w-80' ] ) }}
                        @endif
                    </fieldset>

                    {{-- カレンダー種別 --}}
                    <div class="w-100">
                        <div class="btn btn-outline m-1"
                            data-toggle="collapse"
                            data-target="#search_calendars"
                            aria-expand="true"
                            aria-controls="search_calendars">カレンダー検索条件</div>
                        
                        
                        <fieldset class="col-11 border border-dark m-1 p-1 collapse" id="search_calendars">
                            <div class="m-2">検索対象カレンダー</div>
                            
                            @foreach( Calendar::getTypes() as $type => $name )
                                @php 
                                    $checked = ( ! empty( op( $request->calendar_types )[$type] )) ? 1 : 0;
                                @endphp
                                <label for="{{ $type }}">{{ $name }}</label>
                                {{ Form::checkbox( "calendar_types[$type]", $type,  $checked, [ 'id' => $type, "class" => "calendar_types checkboxradio" ] ) }}
                            @endforeach
    
                            <div class="m-2">閲覧制限カレンダーのアクセス権限</div>
                            {{ Form::select( "calendar_auth", $calendar_auths,  $request->calendar_auth, [  "class" => "form-control" ] ) }}
                            
                            
                            <div class="m-2">その他の条件</div>
                            <label for="show_hidden_calendar">非表示カレンダーも検索</label>
                            @php 
                                $checked = ( $request->show_hidden_calendar ) ? 1 : 0;
                            @endphp
                            {{ Form::checkbox( "show_hidden_calendar", 1,  $checked, [ 'id' => 'show_hidden_calendar', "class" => "checkboxradio m-1" ] ) }}
    
                            @php 
                                $checked = ( $request->show_retired_users ) ? 1 : 0;
                            @endphp
                            <label for="show_retired_users">退社済社員も検索</label>
                            {{ Form::checkbox( "show_retired_users", 1,  $checked, [ 'id' => 'show_retired_users', "class" => "checkboxradio m-1" ] ) }}
                        </fieldset>
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
