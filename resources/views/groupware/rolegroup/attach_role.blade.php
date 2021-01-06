@extends('layouts.app')

@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;



$users = User::whereIn( 'id', request()->input('users') )->get();
$role_groups = toArrayWithNull( RoleGroup::all() );


#dump( $users, $role_groups, 'aaa' );


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

                    
                    {{ Form::open( [ 'route' => 'groupware.role_group.attach_role', 'id' => 'form_attach_role' ] ) }}
                        @method( 'POST' )
                        @csrf

                        <div class="row">
                            <label class="col-2 m-2">割当ロールグループ</label>
                            {{ Form::select( 'role_group', $role_groups, old( 'role_group' ), [ 'class' => 'form-control col-5 m-2', 'required' ] ) }}
                            <div class="btn btn-primary m-2 col-2" id="submit_btn">ロール割当実行</div>

                            <script>
                                $('#submit_btn').click( function() {
                                    $('#form_attach_role').submit();
                                });
                                
                                
                            </script>
                            
                        </div>
                        <hr>
                        <table class="table table-striped m-2 p-1 border clearfix">
                            <tr class="">
                                <th class="">社員名</th>
                                <th class="">部署</th>
                                <th class="">役職</th>
                                <th class="">割当ロール</th>
                            </tr>
                            
                                @foreach( $users as $user )
                                    <tr class="">
                                        {{ Form::hidden( 'users[]', $user->id  ) }}
                                        <td class="">{{ $user->name       }}</td>
                                        <td class="">{{ $user->dept->name }}</td>
                                        <td class="">{{ $user->grade      }}</td>
                                        <td class=""></td>
                                    </tr>
                                @endforeach
                        </table>
                    {{ Form::close() }}
    
                    <div>
                        {{ BackButton::form() }}
                    </div>



                </div>
            </div>
        </div>
    </div>
</div>


@endsection
