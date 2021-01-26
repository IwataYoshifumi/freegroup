@php
use App\Http\Helpers\BackButton;
@endphp

@if( auth( 'user' )->id() == $report->user->id )
    <div class="row m-1 w-100 container">
        <a class="btn btn-warning col-2 col-lg-2 m-1" href="{{ route( 'groupware.report.edit', [ 'report' => $report->id ] ) }}">
            <div class="d-block d-lg-none">変更</div>
            <div class="d-none d-lg-block">変更</div>
        </a>
    
        <a class="btn btn-danger col-2 col-lg-2 m-1" href="{{ route( 'groupware.report.delete', [ 'report' => $report->id ] ) }}">
            <div class="d-block d-lg-none">削除</div>
            <div class="d-none d-lg-block">削除</div>
        </a>
    </div>
@endif
