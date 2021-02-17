@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;

use App\Http\Helpers\BackButton;

$array_user_form = [  
    'form_name' => "find[user_id]", 
    'user_id'   => optional( $find )['user_id'],
    'index'     => 1,
    ];

$array_role_select = ACL::get_array_roles_for_select();

@endphp

{{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    <div class="border border-dark m-1">
        <div class="row">        
            <div class="col-3 p-3 m-2">
                {{-- User_id --}}
                <x-select_user :array="$array_user_form" />
            </div>
            <div class="col-8 p-3 m-2">
                <div class='row'>
                    {{-- アクセス権限 --}}
                    <fieldset class="border border-dark col-12">
                        @foreach( $array_role_select as $role => $value )
                            @if( empty( $role )) @continue @endif
                            @php
                                $form_id = "checkbox-".$role;
                            @endphp
                            <label for="{{ $form_id }}" class="m-1">{{ $value }}</label>
                            {{ Form::checkBox( "find[role][$role]", $role, optional( optional( $find )['role'])[$role], [ 'id' => $form_id, 'class' => 'checkbox_class' ] ) }}
                        @endforeach
                    </fieldset>
                    @if( is_debug() ) 
                        {{-- アクセス権ないものも検索 --}}
                        <fieldset class="border border-dark col-12 mt-1">
                            <label for="search_all" class="m-1">アクセス権ないものも検索</label>
                            {{ Form::checkBox( "find[all]", 1, optional( $find )['all'], [ 'id' => 'search_all', 'class' => 'checkbox_class' ] ) }}
                        </fieldset>
                    @endif
                </div>
            </div>            
    
        </div>
        <div class="col-12 m-2">
            <a class="btn btn-secondary text-white m-1" id="submit_btn">検索</a>
            
        </div>
    </div>
{{ Form::close() }}

<hr>

@push( 'select_user_component_javascript' )
    <script>
        $('#submit_btn').click( function() {
           $('#search_form').submit(); 
        });
        
        $( function() {
            $( "input[type='checkbox']").checkboxradio();
            $( "input[type='radio']"   ).checkboxradio();
        } );
        
        
        
    </script>
@endpush


