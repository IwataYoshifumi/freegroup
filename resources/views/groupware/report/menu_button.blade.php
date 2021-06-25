@php

use App\myHttp\GroupWare\Models\Report;

$options = [ 'from_menu' => 1 ];

$auth = auth( 'user' )->user();

@endphp

<div class="row m-1 w-100 container">
    
    @if( $auth->can( 'create', Report::class ))
        <a class="btn btn-primary col col-lg-2 m-1" href="{{ route( 'groupware.report.create'   ) }}">
            <div class="d-block d-lg-none">新規日報</div>
            <div class="d-none d-lg-block">新規日報作成</div>
        </a>
    @else
        @php
            $route = route( 'groupware.report_list.index' );
            $title = "日報作成権限のある日報リストがありません。日報リストを作成するか、日報リスト管理者に権限割当を依頼してください";
        @endphp
        <a class="btn btn-warning border-dark col-2 col-lg-3 m-1 uitooltip" href="{{ $route }}" title="{{ $title }}">日報リスト作成</a>
        
        
    @endif

    {{--
    <a class="btn btn-menu col col-lg-2 m-1" href="{{ route( 'groupware.report.index', $options  ) }}">
    --}}
    <a class="btn btn-menu col col-lg-2 m-1" href="{{ route( 'groupware.show_all.index', [ 'writable_report_list' => 1, 'set_defaults' => 1 ]  ) }}">
        <div class="d-block d-lg-none">検索</div>
        <div class="d-none d-lg-block">日報検索</div>
    </a>
    
    <a class="btn btn-menu col-2 col-lg-2 m-1 d-none d-lg-block" href="{{ route( 'groupware.report_list.index', $options  ) }}">
        <div>日報リスト</div>
    </a>
        
</div>
