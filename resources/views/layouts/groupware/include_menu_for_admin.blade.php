@php

if( ! Auth( 'admin' )) { return "no admin_menu"; }
$wrap = function( $exp ) { return $exp; };
$route = [  0  => route( 'admin.index' ),
            1  => route( 'groupware.user.index' ),
            3  => route( 'dept.index'),
            4  => route( 'groupware.role_group.index' ),
            ];
@endphp

@if( Auth( 'admin' ) )
    <a class="nav-item nav-link text-white" href="{{ $route[1] }}">社員管理</a>
    <a class="nav-item nav-link text-white" href="{{ $route[3] }}">部署管理</a>
    <a class="nav-item nav-link text-white" href="{{ $route[4] }}">ロール設定</a>
    <a class="nav-item nav-link text-white" href="{{ $route[0] }}">管理者管理</a>
@endif