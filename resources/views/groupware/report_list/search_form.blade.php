@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ReportList;

use App\Http\Helpers\BackButton;

$route_name = Route::currentRouteName();
$auth = auth( 'user' )->user();

$report_list_types = ReportList::getTypes();
$report_list_auths = [  'owner' => '管理者', 'canWrite' => '日報追加可能', 'canRead' => '日報閲覧のみ' ];

$array_role_select = ACL::get_array_roles_for_select();

@endphp

{{ Form::open( [ 'route' => $route_name, 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    <div class="border border-dark m-1">
        <div class="row container">        
            <div class="col-12 m-1 p-1 container" style="color:red">@icon( debug )  開発モード中はログインユーザの検索は外されます</div>

            <div class="col border border-dark m-1 p-1 container">
                日報リスト名
                {{ Form::text( 'name', $request->name, [ 'class' => 'form-control container' ] ) }}
            </div>

            {{-- ユーザ検索 --}}
            <fieldset class="col-4 border border-dark m-1 p-1">
                    <div class="m-2">日報リストアクセス権限検索</div>

                    @foreach( $report_list_auths as $user_auth => $name ) 
                        @php
                            $id      = "user_auth_". $user_auth;
                            $checked =  ( $request->user_auth == $user_auth ) ? 1 : 0;
                            #if_debug( $request->user_auth, $user_auth );
                        @endphp
                        <label for="{{ $id }}">{{ $name }}</label>
                        {{ Form::radio( 'user_auth', $user_auth, $checked, [ 'class' => 'checkboxradio', 'id' => $id ] ) }}
                    @endforeach

                    <div class="m-2">検索対象社員<span title="自分は必ず検索対象に含まれます" class="m-1 uitooltip">@icon( info-circle )</span></div>
                    <x-checkboxes_users :users="op( $request )->users" name='users' button="社員検索" />
            </fieldset>


            <fieldset class="col-4 border border-dark m-1 p-1">
                    <div class="m-2">検索対象日報リスト</div>
                    @php
                        $array = ( is_array( $request->types )) ? $request->types : [];
                    @endphp
                    
                    @foreach( $report_list_types as $type => $name ) 
                        @php
                            $id      = "report_list_type_". $type;
                            $checked =  ( in_array( $type, $array )) ? 1 : 0;
                        @endphp
                        <label for="{{ $id }}">{{ $name }}</label>
                        {{ Form::checkbox( 'types[]', $type, $checked, [ 'class' => 'checkboxradio', 'id' => $id ] ) }}
                    @endforeach


                    @php 
                        $checked = ( $request->show_hidden ) ? 1 : 0;
                    @endphp
                    <label for="show_hidden">非表示日報リストも検索</label>
                    {{ Form::checkbox( "show_hidden", 1,  $checked, [ 'id' => 'show_hidden', "class" => "checkboxradio m-1" ] ) }}

                    @php 
                        $checked = ( $request->show_disabled ) ? 1 : 0;
                    @endphp
                    <label for="show_disabled">無効日報リストも検索</label>
                    {{ Form::checkbox( "show_disabled", 1,  $checked, [ 'id' => 'show_disabled', "class" => "checkboxradio m-1" ] ) }}
                    
                    
            </fieldset>





                        
    
        </div> {{-- close row --}}

        <div class="col-12 m-1 container">
            <a class="btn btn-secondary text-white m-1" id="submit_btn">検索</a>
        </div>
    </div>
{{ Form::close() }}
<hr>
<script>
    $('#submit_btn').on( 'click', function() { $('#search_form').submit(); })
    
</script>

