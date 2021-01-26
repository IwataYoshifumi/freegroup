<div class="m-2">
    <div class="d-none d-lg-block">
        <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'groupware.calendar.index'  ) }}">カレンダー一覧</a>
        <a class="btn btn-primary           col-3 m-1" href="{{ route( 'groupware.calendar.create' ) }}">新規カレンダー</a>
        @if( is_debug() )
            <a class="btn btn-warning       col-2 icon_btn" href="{{ route( 'groupware.calprop.gsync_all' ) }}" title="Googleカレンダー全同期">
                <i class="fab fa-dev icon_debug"></i><i class="fas fa-sync-alt icon_btn ml-3"></i>
            </a>
        @endif
        
    </div>
</div>
