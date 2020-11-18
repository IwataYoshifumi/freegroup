@php
    $options = [ 'from_menu' => 1 ];

@endphp


<div class="row m-1 w-100 container">
    @if( $file->user->id == auth( 'user' )->id() ) 
        <a class="btn btn-danger col-2 col-lg-2 m-1" href="{{ route( 'groupware.file.delete', [ 'files[]' => $file ]  ) }}">
    
            <div class="d-block d-lg-none">ファイル削除</div>
            <div class="d-none d-lg-block">削除</div>
        </a>
    @endif
</div>
