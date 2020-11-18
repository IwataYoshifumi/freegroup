@php

    $auth = auth('admin')->user();
    if( ! $auth ) { $auth = auth('user')->user(); }
    
    //dd( auth( 'user' )->user() );
@endphp
<div class="m-2 row">
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vindex' 
            href='{{ route( 'vacation.common.monthly' , [ 'root_route' => 1 ] ) }}'>休暇検索（月表示）</a>

    <a class='btn btn-success m-1 col-5 col-lg-2' id='vindex' 
            href='{{ route( 'vacation.common.vindex' , [ 'root_route' => 1 ] ) }}'>休暇検索</a>
    @if( optional( $auth )->is_admin() or $auth->browsing() != "自分のみ" ) 
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vacation ' 
            href='{{ route( 'vacation.common.no_vacation', ['root_route' => 1] ) }}'>有給 【未取得者】</a>
    @endif        
    
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vacation ' 
            href='{{ route( 'vacation.common.how_many_days_get_for_paidleave', ['root_route' => 1] ) }}'>有給 【取得日数】</a>
    
    <a class='btn btn-success m-1 col-5 col-lg-2' id='vacation ' 
            href='{{ route( 'vacation.common.how_many_days_left_for_paidleave', ['root_route' => 1] ) }}'>有給 【残日数】</a>
</div>