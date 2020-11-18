@php
    $auth = auth('admin')->user();
    if( ! $auth ) { $auth = auth('user')->user(); }
@endphp
<div class="m-2 row">
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vindex' 
            href='{{ route( 'vacation.common.vindex' , [ 'root_route' => 1 ] ) }}'>休暇検索</a>
    @if( $auth->is_admin() or $auth->browsing() != "自分のみ" ) 
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vacation ' 
            href='{{ route( 'vacation.common.no_vacation', ['root_route' => 1] ) }}'>有給未取得者検索</a>
    @endif        
    
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vacation ' 
            href='{{ route( 'vacation.common.how_many_days_left_for_paidleave', ['root_route' => 1] ) }}'>有給残日数</a>
</div>