@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Depts;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;

$access_list = $group->access_list();

#dump( $group, $lists, $users, $group->users );
@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.group.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                        
                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <div class=ui-state-error ui-corner-all" style='padding: 5px'>
                            <span class="ui-icon ui-icon-alert"></span>
                            このグループを削除します。よろしいですか。
                        </div>

                        @include( 'groupware.group.show_parts' )

                        <!--
                        //　削除確認用フォーム
                        //
                        -->
                        <div class="form-group row mb-0">
                            @if( Route::currentRouteName() == "groupware.group.delete" )
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
                                    });
                                </script>
                                <a class="btn btn-danger text-white ml-5" onClick="submit_button();">グループ削除</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
