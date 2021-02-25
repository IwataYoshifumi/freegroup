@php

use App\myHttp\GroupWare\Models\User;

@endphp
<div class="m-1 w-100 container">
    @can( 'create', User::class )
        <a class="btn btn-primary col-2 m-1" href="{{ route( 'groupware.user.create' ) }}">新規　社員登録</a> 
    @endcan
    <a class="btn btn-outline-secondary col-2 m-1" href="{{ route( 'groupware.user.index' ) }}">社員一覧</a>
    
    @if( auth( 'admin' )->check() )
        @php
            $title = "DB初期化処理";
            $route = route( 'groupware.init.all_users' );
            // $route = route( 'groupware.user.index' );
        @endphp
        
        <div class="col-1 btn icon_btn uitooltip"              id="toggle_btn" title="{{ $title }}">@icon( caret-down )</div>
        <a   class="col-1 btn btn-danger text-white uitooltip" id="init_btn"   title="{{ $title }}" href="{{ $route }}">DB初期化</a>
        
        <script>
            $("#init_btn").hide();
            
            $('#toggle_btn').on( 'click', function() {
                $('#init_btn').toggle( {} ); 
            });
            
        </script>
    @endif
    
</div>
