@php

use App\myHttp\GroupWare\Models\Calendar;

@endphp

<div class="m-2">
    <div class="d-none d-lg-block">
        <div class="row">
        
            <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.calendar.index'  ) }}">カレンダー一覧</a>
            
            @can( 'create', Calendar::class )
                <a class="btn btn-primary col-4 m-1" href="{{ route( 'groupware.calendar.create' ) }}">新規カレンダー</a>
            @elsecan
                @php
                $route = route( 'groupware.access_list.index' );
                $title = "自分が管理者であるアクセスリストがないため、カレンダーを作成できません。カレンダー作成前にアクセスリストを作成してください";
                @endphp
                <a class="btn btn-warning border border-dark col m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">アクセスリスト作成</a>    
            @endcan
        </div>
    </div>
</div>
