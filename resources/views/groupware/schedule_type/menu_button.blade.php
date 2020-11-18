@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Controllers\ScheduleController;

@endphp

<div class="row m-1 w-100 container">

    <a class="btn btn-primary text-white col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.type.create' ) }}">
        <div class="d-block d-lg-none">新規</div>
        <div class="d-none d-lg-block">新規スケジュール種別</div>
    </a>

    <a class="btn btn-menu col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.type.index' ) }}">
        <div class="d-block d-lg-none">一覧</div>
        <div class="d-none d-lg-block">スケジュール種別一覧</div>
    </a>

</div>
