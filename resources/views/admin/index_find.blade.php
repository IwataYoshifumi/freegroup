@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Admin;

@endphp

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'SearchQuery', 1 ) }} 
    @csrf
                
    <div class="container border border-dark p-1 w-95 m-1 p-1">
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-4 d-none d-lg-block p-1">名前</div>
            <div class="col-4 d-none d-lg-block p-1">メール</div>
            <div class="col-4 d-none d-lg-block p-1">退職</div>
            
        </div>
        <div class="row p-1 container m-1">
            <div class="col-12 d-lg-none p-1">名前</div>
            {{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                            ['class' => 'form-control col-lg-4 p-1 clearfix', 'placeholder' => '名前' ] ) }}
            <div class="col-12 d-lg-none p-1">メール</div>

            {{ Form::text( 'find[email]', old( 'find[email]', ( isset( $find['email'] )) ? $find['email'] : null  ), 
                            ['class' => 'form-control col-lg-4 p-1 clearfix', ] ) }}
            <div class="col-4 d-lg-none m-2 p-1">退職</div>
            {{ Form::select( 'find[retired]', [ "" => "", 0 => "在職", 1 => "退社", 'all' => "全て" ] , 
                                            old( 'find[retired]', ( isset( $find['retired'] )) ? $find['retired'] : "" ),
                                            ['class' => 'form-control col-6 col-lg-2 m-2 m-lg-0 p-1 clearfix' ] )  }}

        </div>
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-4 d-none d-lg-block p-1">表示数</div>
            <div class="col-4 d-none d-lg-block p-1"></div>
            <div class="col-4 d-none d-lg-block p-1"></div>
        </div>

        <div class="row w-100 container p-1 m-1">        
            <div class="col-4 d-lg-none m-2 p-1">表示数</div>
                {{ Form::select( 'find[paginate]', config( 'constant.pagination' ),
                                    old( 'find[paginate]', ( isset( $find['paginate'] )) ? $find['paginate'] : ""  ),
                                    ['class' => 'form-control col-6 col-lg-2 m-2 m-lg-0 p-1' ] )  }}
        </div>
        

    </div>
        
    <div class='container border border-dark m-1'>
        <a class="col-12 btn btn-sm btn-outline text-left" data-toggle="collapse" href="#show_items" role="button" aria-expanded="true" aria-controls="collapseExample">
            表示項目<div class="navbar-toggler-icon"></div>
        </a>
        <div class="col-12"></div>
        <div class="container w-100 m-1 collapse" id="show_items">
            <div class='row'>
                @php 
                    $show_items   = [ 'email', 'retired', 'date_of_retired'  ];
                    // if_debug( config( 'user.columns_name' ));
                    //if_debug( $show );
                @endphp
    
                @foreach( $show_items as $item ) 
                    @php
                        ( in_array( $item, $show, true )) ? $checked = 1 : $checked = 0 ;
                    @endphp
                    <div class='col'>{{ Form::checkbox( "show[".$item."]", $item, $checked ) }} {{ config( 'user.columns_name')[$item] }}</div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class='container border border-dark m-1'>
        <a class="col-12 btn btn-sm btn-outline text-left" data-toggle="collapse" href="#sort" role="button" aria-expanded="true" aria-controls="collapseExample">
            ソート<div class="navbar-toggler-icon"></div>
        </a>
        <div class="col-12"></div>
        <div class="container w-100 m-1 collapse" id="sort">
            <div class='row'>
                @php 
                    $sort_items   = [ '' => '', 'name' => '名前',  'email' => 'メール', 'retired' => '退社' ];
                    //$sort_items = config( 'user.columns.name' );
                @endphp
    
                @for( $i = 0; $i <= 2; $i++ ) 
                    {{ Form::select( "sort[$i]", $sort_items, old( "sort[".$i."]", ( ! empty( $sort[$i] )) ? $sort[$i] : null ),
                                    [ 'class' => 'form-control col-3' ] ) }}  

                @endfor
                <div class="col-12"></div>
                @for( $i = 0; $i <= 2; $i++ ) 
                    {{ Form::select( "asc_desc[$i]", config( 'constant.asc_desc' ), old( "asc_desc[".$i."]", ( ! empty( $asc_desc[$i] )) ? $asc_desc[$i] : null ),
                                    [ 'class' => 'form-control col-3' ] ) }}  

                @endfor
            
            </div>
        </div>

    </div>
    <div class="row w-100 container p-1 m-1">
        <button type="submit" class="btn btn-search col-6 col-lg-3">検索</button>
    </div>
{{ Form::close() }}
