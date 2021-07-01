@php
use App\myHttp\GroupWare\Models\ReportList;

@endphp

<div class="m-2">
    <div class="d-none d-lg-block">

        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.report_list.index'  ) }}">日報リスト一覧</a>
        
        @if( $auth->can( 'create', ReportList::class ))
            <a class="btn btn-primary           col-3 m-1" href="{{ route( 'groupware.report_list.create' ) }}">日報リスト　新規作成</a>
        @else
            @php
                $route = route( 'groupware.access_list.index' );
                $title = "自分が管理者であるアクセスリストがないため、カレンダーを作成できません。カレンダー作成前にアクセスリストを作成してください";
            @endphp
            <a class="btn btn-warning border border-dark col-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">アクセスリスト作成</a>  
            
        @endif
    </div>
</div>
