@php
    use App\Models\Vacation\Vacation;
@endphp

<div class="m-2 row">
    <a class='btn btn-success m-1 col-4 col-lg-3' id='create' href='{{ route( 'vacation.application.create' ) }}'>休暇申請</a>
    @if( Vacation::is_valid_hourly_paidleave() ) 
        <a class='btn btn-success m-1 col-4 col-lg-3' id='create' href='{{ route( 'vacation.application.create_hourly' ) }}'>時間有給申請</a>
    @endif
    
    
    <a class='btn btn-success m-1 col-4 col-lg-3' id='index ' href='{{ route('vacation.application.index', ['root_route' => 1] ) }}'>
        <div class="d-none d-sm-block">休暇申請【一覧】</div>
        <div class="       d-sm-none" >休暇一覧</div>
        
    </a>
</div>