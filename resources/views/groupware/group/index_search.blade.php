@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;


use App\Http\Helpers\BackButton;

$array_user_form = [  
    'form_name' => "find[access_list][user_id]", 
    'user_id'   => op( op( $find )['access_list'])['user_id'],
    'index'     => 1,
    ];

$array_role_select = ACL::get_array_roles_for_select();

@endphp

{{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    <div class="border border-dark m-1 container-fluid">
        <div class="row">        
            
            <div class="col-12">
                <a class="btn btn-secondary text-white m-2" onClick="submit_btn_1()">自分が管理者のグループを検索</a> 
            </div>
            
            <div class="col-11 col-md-3 pt-1 m-2" id="search_access_list_user">
                {{-- User_id --}}
                <x-select_user :array="$array_user_form" />
            </div>
            
            <div class="col-11 col-md-8 pt-1 m-2" id="search_access_list_role">
                <div class='row'>
                    {{-- アクセスリスト検索 --}}
                    <fieldset class="border border-dark col-12">
                        @foreach( $array_role_select as $role => $value )
                            @if( empty( $role )) @continue @endif
                            @php
                                $form_id = "checkbox-".$role;
                                $array = op( op( $find )['access_list'] )['role'];
                                
                            @endphp
                            <label for="{{ $form_id }}" class="m-1">{{ $value }}</label>
                            {{ Form::checkBox( "find[access_list][role][$role]", $role, op( $array )[$role], [ 'id' => $form_id, 'class' => 'checkbox_class' ] ) }}
                        @endforeach
                    </fieldset>
                    {{-- アクセス権ないものも検索 --}}
                    <fieldset class="border border-dark col-12 mt-1">
                        <label for="search_all" class="m-1">アクセス権ないものも検索</label>
                        {{ Form::checkBox( "find[all]", 1, op( $find )['all'], [ 'id' => 'search_all', 'class' => 'checkbox_class' ] ) }}
                    </fieldset>
                </div>
            </div>            

            <hr class="w-90">
            <div class="col-12">
                <a class="btn btn-outline-secondary m-2" onClick="runEffect();">グループ内のユーザを検索</a> 

            </div>

            <!--- コンポーネント InputCustomersComponent --->

            <div class="col-6 m-3" id="search_group_users">
                <div class="col-12">
                    <a class="btn btn-secondary text-white m-2" onClick="submit_btn_2()">自分が含まれるグループを検索</a> 
                </div>
                <x-input_users :users="op( $find )['users']"/>
            </div>


    
        </div>
        <div class="col-12 m-2">
            <a class="btn btn-secondary text-white m-1" onClick="submit_search_form()">検索</a>
            
        </div>
    </div>
{{ Form::close() }}

<hr>

@push( 'select_user_component_javascript' )
    
    {{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET', 'id' => 'search_form_1' ] ) }}
        @csrf
        <input type="hidden" name="find[access_list][role][owner]" value="owner">
        <input type="hidden" name="find[access_list][user_id]" value="{{ auth('user')->id() }}">
    {{ Form::close() }}

    {{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET', 'id' => 'search_form_2' ] ) }}
        @csrf
        <input type="hidden" name="users[]" value="{{ auth('user')->id() }}">
    {{ Form::close() }}


    <script>
        function submit_btn_1() {
           $('#search_form_1').submit(); 
        }
        
        function submit_btn_2() {
            $('#search_form_2').submit();
        }

        function submit_search_form() {
           $('#search_form').submit(); 
        }
        
        function runEffect() {
            var effect = "blind";
            var options = {};
            $("#search_group_users").toggle( effect, options, 500 );
        }

        $( function() {
            $( "input[type='checkbox']").checkboxradio();
            $( "input[type='radio']"   ).checkboxradio();
            $("#search_group_users").hide();
        } );

        
    </script>
@endpush


