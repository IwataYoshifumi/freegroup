@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Facility;

use App\Http\Helpers\BackButton;

$array_user_form = [  
    'form_name' => "find[user_id]", 
    'user_id'   => op( $find )['user_id'],
    'index'     => 1,
    ];

$facility_types = Facility::getTypes();
// $search_auths = [ '' => '',  'isOwner' => '管理者', 'canWrite' => 'タスク追加可能', 'canRead' => 'タスク検索・閲覧可能' ];
$search_auths = [ '' => '',  'owner' => '設備管理者', 'writer' => '設備予約可能', 'reader' => '設備予約状況・閲覧可能' ];


# dump( $array_user_form, $facility_types );

$array_role_select = ACL::get_array_roles_for_select();

$categories = Facility::getCategories();

if_debug( $categories );

@endphp

{{ Form::open( [ 'route' => 'groupware.facility.index' , 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    <div class="border border-dark m-1 container-fluid">
        <div class="row no-gutters">        
            <div class="col-12 col-md-3 p-3 m-2">
                {{-- User_id --}}
                <x-select_user :array="$array_user_form" />
            </div>
            <div class="col-12 col-md-8 p-3 m-2">
                <div class='row'>
                    {{-- アクセス権限 --}}
                    <fieldset class="border border-dark col-12 p-1">
                        <div>アクセスリスト設定権限</div>
                        {{ Form::select( "find[auth]", $search_auths, op( $find )['auth'], [ 'class' => 'form-control col-12 col-md-5' ] ) }}
                        
                        <div class="col-12">公開種別</div>
                        @foreach( $facility_types as $type => $value )
                            @if( empty( $type )) @continue @endif
                            @php
                                $form_id = "checkbox-".$type;
                            @endphp
                            <label for="{{ $form_id }}" class="m-1">{{ $value }}</label>
                            {{ Form::checkBox( "find[type][$type]", $type, optional( optional( $find )['type'])[$type], [ 'id' => $form_id, 'class' => 'checkbox_class' ] ) }}
                        @endforeach
                        <div class="col-12 mt-1">その他検索条件</div>
                        <label for='disabled'>無効設備</label>
                        {{ Form::checkBox( "find[disabled]", 1, op( $find )['disabled'], [ 'id' => 'disabled', 'class' => 'checkbox_class' ] ) }}
                        
                        
                    </fieldset>

                </div>
            </div>            
    
        </div>
        <div class="col-12 m-2">
            <a class="btn btn-secondary text-white m-1" id="submit_btn">検索</a>
        </div>
    </div>
{{ Form::close() }}

<hr>

<script>
    $('#submit_btn').click( function() {
       $('#search_form').submit(); 
    });
    
    $( function() {
        $( ".checkbox_class").checkboxradio();
        $( '#not_use' ).checkboxradio( { icon: false } );
        $( '#disabled').checkboxradio( { icon: false } );
    } );
</script>
