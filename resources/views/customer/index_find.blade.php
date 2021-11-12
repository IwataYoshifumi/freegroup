@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Customer;
#if_debug( $find, $show, $sort );

@endphp

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'SearchQuery', 1 ) }} 
    @csrf
    
    <!-- 検索条件 -->            
    <div class="container border border-dark p-1 w-95 m-1 p-1">
        <a class="col-12 btn btn-sm btn-outline text-left" data-toggle="collapse" data-target="#search_box"  role="button" aria-expanded="true">
            検索条件
        </a>
        <div class="collapse" id="search_box">
            <div class="row w-90 container m-lg-1 p-lg-1 ">
                <div class="col-4 d-none d-lg-block p-1">名前</div>
                <div class="col-4 d-none d-lg-block p-1">メール</div>
            </div>
            <div class="row p-1 container m-1">
                <div class="col-12 d-lg-none p-1">名前</div>
                {{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                                ['class' => 'form-control col-lg-4 p-1 clearfix', 'placeholder' => '名前' ] ) }}
                <div class="col-12 d-lg-none p-1">メール</div>
    
                {{ Form::text( 'find[email]', old( 'find[email]', ( isset( $find['email'] )) ? $find['email'] : null  ), 
                                ['class' => 'form-control col-lg-4 p-1 clearfix', 'placeholder' => 'メール' ] ) }}
            </div>
            <div class="row w-90 container m-lg-1 p-lg-1">
                <div class="col-3 d-none d-lg-block p-1">都道府県</div>
                <div class="col-3 d-none d-lg-block p-1">市区町村</div>
                <div class="col-3 d-none d-lg-block p-1">町名番地</div>
            </div>
            <div class="row p-1 container m-1">
                <div class="col-12 d-lg-none p-1">都道府県</div>
                {{ Form::text( 'find[prefecture]', old( 'find[prefecture]', ( isset( $find['prefecture'] )) ? $find['prefecture'] : "" ), 
                                ['class' => 'form-control col-lg-3 p-1 clearfix', 'placeholder' => '都道府県' ] ) }}
                <div class="col-12 d-lg-none p-1">市区町村</div>
                {{ Form::text( 'find[city]', old( 'find[city]', ( isset( $find['city'] )) ? $find['city'] : "" ), 
                                ['class' => 'form-control col-lg-3 p-1 clearfix', 'placeholder' => '市区町村' ] ) }}
                <div class="col-12 d-lg-none p-1">町名・番地</div>
                {{ Form::text( 'find[street]', old( 'find[street]', ( isset( $find['street'] )) ? $find['street'] : "" ), 
                                ['class' => 'form-control col-lg-3 p-1 clearfix', 'placeholder' => '町名・番地' ] ) }}
            </div>
    
            <div class="row w-90 container m-lg-1 p-lg-1 ">
                <div class="col-4 d-none d-lg-block p-1">電話番号</div>
                <div class="col-4 d-none d-lg-block p-1">表示数</div>
                <div class="col-4 d-none d-lg-block p-1"></div>
            </div>
    
            <div class="row w-100 container p-1 m-1">        
                <div class="col-12 d-lg-none p-1">電話番号</div>
                {{ Form::text( 'find[telephone]', old( 'find[telephone]', ( isset( $find['telephone'] )) ? $find['telephone'] : "" ), 
                                ['class' => 'form-control col-lg-3 p-1 clearfix', 'placeholder' => '電話番号' ] ) }}
            
            
                <div class="col-4 d-lg-none m-2 p-1">表示数</div>
                    {{ Form::select( 'find[paginate]', config( 'constant.pagination' ),
                                        old( 'find[paginate]', ( isset( $find['paginate'] )) ? $find['paginate'] : ""  ),
                                        ['class' => 'form-control col-6 col-lg-2 m-2 m-lg-0 p-1' ] )  }}
            </div>

            <div class="row w-100 container p-1 m-1">
                <button type="submit" class="btn btn-search col-6 col-lg-3">検索</button>
            </div>
                
            
        </div>
    </div>
        
    <!-- 表示項目 -->
    <div class='container border border-dark m-1'>
        <a class="col-12 btn btn-sm btn-outline text-left enable_search_button" data-toggle="collapse" href="#show_items" role="button" aria-expanded="true" aria-controls="collapseExample">
            表示項目<div class="navbar-toggler-icon"></div>
        </a>
        <div class="col-12"></div>
        <div class="container w-100 m-1 collapse" id="show_items">
            <div class='row'>
                @php 
                    $show_items   = [ 'email', 'kana', 'zip_code', 'address', 'birth_day', 'tel', 'fax', 'mobile', 'sex' ];
                    // if_debug( config( 'customer.columns_name' ));
                    //if_debug( $show );
                @endphp
    
                @foreach( $show_items as $item ) 
                    @php
                        ( in_array( $item, $show, true )) ? $checked = 1 : $checked = 0 ;
                    @endphp
                    <label for="show_{{ $item }}">{{ optional( config( 'customer.columns_name'))[$item] }}</label>
                    {{ Form::checkbox( "show[".$item."]", $item, $checked, [ 'id' => "show_". $item, 'class' => 'checkboxradio' ] ) }}
                    
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- ソート -->
    <div class='container border border-dark m-1'>
        <a class="col-12 btn btn-sm btn-outline text-left enable_search_button" data-toggle="collapse" href="#sort" role="button" aria-expanded="true" aria-controls="collapseExample">
            ソート<div class="navbar-toggler-icon"></div>
        </a>
        <div class="col-12"></div>
        <div class="container w-100 m-1 collapse" id="sort">
            <div class='row'>
                @php 
                    $sort_items   = [ '' => '', 'name' => '名前',  'address' => '住所', 'email' => 'メール', 'birth_day' => '誕生日' ];
                    
                    //$sort_items = config( 'customer.columns.name' );
                @endphp
    
                @for( $i = 0; $i <= 2; $i++ )
                    @php $j = $i + 1; @endphp
                    <div class="d-none d-lg-block col-2 text-right">ソート順{{ $i + 1 }}：</div>
                    {{ Form::select( "sort[$i]", $sort_items, old( "sort[".$i."]", ( ! empty( $sort[$i] )) ? $sort[$i] : null ),
                                    [ 'class' => 'form-control col-7 col-lg-3' ] ) }}  

                    {{ Form::select( "asc_desc[$i]", config( 'constant.asc_desc' ), old( "asc_desc[".$i."]", ( ! empty( $asc_desc[$i] )) ? $asc_desc[$i] : null ),
                                    [ 'class' => 'form-control col-4 col-lg-2'] ) }}  
                    
                    <div class="col-12 d-none d-lg-block m-1"></div>

                @endfor


            
            </div>
        </div>

    </div>
    
    <div class="row w-100 container p-1 m-1" id="search_button" style="display: none;">
        <button type="submit" class="btn btn-search col-6 col-lg-3">検索</button>
    </div>
    <script>
        $('.enable_search_button').click( function() {
            $('#search_button').css('display', 'block') 
        })
    </script>
    
{{ Form::close() }}
