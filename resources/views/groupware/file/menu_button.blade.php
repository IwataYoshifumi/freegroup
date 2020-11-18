@php
    $options = [ 'from_menu' => 1 ];

@endphp


<div class="row m-1 w-100 container">
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.file.index', $options ) }}">
        <div class="d-block d-lg-none">一覧</div>
        <div class="d-none d-lg-block">ファイル一覧</div>
    </a>

    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.file.select', $options ) }}">
        <div class="d-block d-lg-none">削除</div>
        <div class="d-none d-lg-block">ファイル削除</div>
    </a>
        
</div>
