
<div class="m-2">
    <div class="d-none d-lg-block">
        @if( auth('admin')->id() )
            
            <a class="btn btn-primary           col-3 m-1" href="{{ route( 'dept.create' ) }}">部署【新規登録】</a>
        @endif
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'dept.index'  ) }}">部署【一覧】</a>
    </div>
    <div class="d-block d-lg-none">
        @if( auth('admin')->id() )
            <a class="btn btn-primary           col-3 m-1" href="{{ route( 'dept.create' ) }}">新規</a>
        @endif
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'dept.index'  ) }}">一覧</a>
    </div>
</div>
