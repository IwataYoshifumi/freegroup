@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;

// $acls = $access_list->acls;
$acls = ACL::with([ 'aclable' ])->where( 'access_list_id', $access_list->id )->orderBy( 'order' )->get();
$roles = ACL::get_array_roles_for_select();

#dump( $access_list, $acls );
#dump( $access_list, $lists, $users, $access_list->users );
@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.access_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @include( 'groupware.access_list.show_parts' )
                            
                            
                        </div>

                        <div class="form-group row mb-0">
                            @if( Route::currentRouteName() == "groupware.access_list.delete" )
                                <form id="delete_form" method="POST" action="{{ url()->current() }}" class="col-12">
                                    @method( 'DELETE' )
                                    @csrf
                                    <div class="row p-1">
                                        <label for="delete_comfirm">削除確認</label>
                                        {{ Form::checkbox( 'delete_comfirm', 1, 0, [ 'class' => 'col-1 m-1', 'id' => 'delete_comfirm' ] ) }} 
                                        <div class="col-12"></div>
                                    </div>
                                    <br>
                                </form>
                                <script>
                                    function submit_button() {
                                        $('#delete_form').submit();
                                    }
                                    $( function() {
                                        $('#delete_comfirm').checkboxradio();
                                    })
                                    
                                </script>
                                <a class="btn btn-danger text-white ml-5" onClick="submit_button();">アクセスリスト削除</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
