@php

use App\myHttp\GroupWare\Models\User;

@endphp
<div class="m-1 w-100 container">
    @can( 'create', User::class )
        <a class="btn btn-primary col-3 m-1" href="{{ route( 'groupware.user.create' ) }}">新規　社員登録</a> 
    @endcan
    <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.user.index' ) }}">社員一覧</a>
</div>
