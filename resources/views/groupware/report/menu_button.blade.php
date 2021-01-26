@php
    $options = [ 'from_menu' => 1 ];

@endphp


<div class="row m-1 w-100 container">
    <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'groupware.report.create'   ) }}">
        <div class="d-block d-lg-none">新規</div>
        <div class="d-none d-lg-block">新規日報作成</div>
    </a>

    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.report.index', $options  ) }}">
        <div class="d-block d-lg-none">一覧</div>
        <div class="d-none d-lg-block">日報一覧</div>
    </a>
    
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.report_list.index', $options  ) }}">
        <div>日報リスト</div>
    </a>
        
</div>
