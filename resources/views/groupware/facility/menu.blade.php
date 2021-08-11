@php

use App\myHttp\GroupWare\Models\Facility;

@endphp

<div class="m-2">
    <div class="d-none d-lg-block">
        
        {{--
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.task.index'  ) }}">タスク一覧</a>
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.show_all.index', [ 'writable_facility' => 1, 'set_defaults' => 1 ]  ) }}">タスク検索</a>
        --}}


        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.reservation.monthly'  ) }}">設備　予約状況</a>

        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.facility.index'  ) }}">設備一覧</a>
        
        @if( $user->can( 'create', Facility::class )) 
            <a class="btn btn-primary col-3 m-1" href="{{ route( 'groupware.facility.create' ) }}">新規設備</a>
        @else
            @php
                $route = route( 'groupware.access_list.index' );
                $title = "自分が管理者であるアクセスリストがないため、設備を作成できません。設備作成前にアクセスリストを作成してください";
            @endphp
            <a class="btn btn-warning border border-dark col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">アクセスリスト作成</a>    
        @endif
        
    </div>
</div>
