@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;


@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.rolegroup.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.rolegroup.select_users_search' )
                    
                    <table class="table table-striped m-2 p-1 border clearfix">
                        <tr class="">
                            <th class="">割当</th>
                            <th class="">社員名</th>
                            <th class="">部署</th>
                            <th class="">役職</th>
                            <th class="">割当ロール</th>
                        </tr>
                        
                        {{ Form::open( [ 'route' => 'groupware.role_group.attach_role', 'id' => 'form_select_users' ] ) }}
                            @method( 'GET' )
                            @csrf
                            @foreach( $users as $user )
                                @php
                                    $href = route( 'groupware.role_group.attach_role', [ 'users[]' => $user->id ] );                                
                                
                                @endphp
                                <tr class="">
                                    <td class="">
                                        {{ Form::checkbox( 'users['.$user->id.']', $user->id, old( 'user['.$user->id.']' ), [ 'class' => 'user-checkbox' ] ) }}
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">ロール変更</a>
                                        
                                    </td>
                                    <td class="">{{ $user->name       }}</td>
                                    <td class="">{{ $user->dept->name }}</td>
                                    <td class="">{{ $user->grade      }}</td>
                                    <td class="">{{ optional( $user->role_group() )->name      }}</td>
                                </tr>
                            @endforeach
                        {{ Form::close() }}
                    </table>
    
                    <div class="btn btn-sm btn-primary check_btn m-2 col-2" id="check_btn" data-checked="0">全てチェック</div>
                    <div class="btn btn-primary m-2 col-2" id="submit_btn">ロール割当画面へ</div>
                    <script>                
                        //　全てチェック・チェックを外すボタン
                        //
                        $('#check_btn').click( function() {
                            $('.user-checkbox').each( function(){
                                // console.log( $(this).val() );
                                if( $('#check_btn').data('checked') == 0 ) {
                                    $(this). prop('checked', true );
                                } else {
                                    $(this). prop('checked', false);
                                }
                            }) 
                            if( $('#check_btn').data('checked') == 0 ) {
                                $('#check_btn').data({checked: 1});
                                $('#check_btn').html('チェックを外す');
                            } else {
                                $('#check_btn').data({checked: 0});
                                $('#check_btn').html('全てチェック');
                            }
                        });
                        
                        
                        
                        //　有給割当ボタン（submit）
                        //
                        //  従業員チェックボックスが選択されているか確認
                        //
                        $('#submit_btn').click( function() {
                            var checked_users = 0;
                            $('.user-checkbox:checked').each( function() {
                               checked_users++; 
                            }); 
                            console.log( checked_users );
                            if( checked_users >= 1 ) {
                               $('#form_select_users').submit();   
                            } else {
                                window.alert('従業員を選択してください')
                            }
                        });
                    </script>
                        

                    <div class="w-100 p-1 m-1">
                        {{ $users->appends( [ 'find' => $find ] )->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
