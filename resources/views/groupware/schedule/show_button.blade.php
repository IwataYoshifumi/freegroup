@php
if( auth( 'user' )->id() != $schedule->user->id ) {
    $disabled = "disabled";
    $tag      = "button";
} else {
    $disabled = "";
    $tag      = "a";
}
@endphp


<div class="row m-1 w-100 container">

    @if( auth( 'user' )->id() == $schedule->user->id )
        <{{ $tag }} class="btn btn-warning col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.edit', [ 'schedule' => $schedule->id ] ) }}" {{ $disabled }}>
            <div class="d-block d-lg-none">変更</div>
            <div class="d-none d-lg-block">変更</div>
        </{{ $tag }}>
        
    
        <{{ $tag }} class="btn btn-danger col-2 col-lg-2 m-1" href="{{ route( 'groupware.schedule.delete', [ 'schedule' => $schedule->id ] ) }}" {{ $disabled }}>
            <div class="d-block d-lg-none">削除</div>
            <div class="d-none d-lg-block">削除</div>
        </{{ $tag }}>
    @endif

    <a class="btn btn-primary text-white col-2 col-lg-2 m-1" href="{{ route( 'groupware.report.create', [ 'schedule_id' => $schedule->id ] ) }}">
        <div class="d-block d-lg-none">新規日報</div>
        <div class="d-none d-lg-block">新規日報</div>
    </a>
</div>
