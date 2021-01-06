<div class="m-2">
    <div class="d-none d-lg-block">
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.calendar.index'  ) }}">カレンダー一覧</a>
        <a class="btn btn-primary           col-3 m-1" href="{{ route( 'groupware.calendar.create' ) }}">新規カレンダー</a>
        @if( is_debug() )
            <a class="btn btn-warning       col-3 m-1" href="{{ route( 'groupware.calprop.gsync_all' ) }}">Googleカレンダー全同期</a>
        @endif
        
    </div>
</div>
