@php
use Carbon\Carbon;

@endphp

<div class="row m-1 w-100 container">

    <a class="btn btn-primary col-2 col-lg-2 m-1" href="{{ route( 'calendar.create' ) }}">
        <div class="d-block d-lg-none">新規</div>
        <div class="d-none d-lg-block">新規予定</div>
    </a>
        
    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'calendar.index' ) }}">
        <div class="d-block d-lg-none">一覧</div><div class="d-none d-lg-block">一覧表示</div>
    </a>
        
    <a class="btn col-1 m-1 ml-auto" href="{{ route( 'groupware.schedule.daily'    ) }}">
        <i class="fas fa-cog" style="font-size: 21px; color: black;"></i>
    </a>
    

</div>
