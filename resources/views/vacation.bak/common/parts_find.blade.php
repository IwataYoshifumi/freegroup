
@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use Illuminate\Support\Facades\Auth; 

$c = new Carbon( 'today' );
$m = (int)$c->format( 'm' );
if( $m <= 3 ) {
    $carbon_start = new Carbon( 'first day of April last year' );
    $carbon_end   = new Carbon( 'last day of March this year' );
    $button['今年度']['start'] = $carbon_start->format('Y-m-d');
    $button['今年度']['end']   = $carbon_end  ->format('Y-m-d');
    $button['昨年度']['start'] = $carbon_start->subYears(1)->format('Y-m-d');
    $button['昨年度']['end']   = $carbon_end  ->subYears(1)->format('Y-m-d');
                                        
            
} else {
    $carbon_start = new Carbon( 'first day of April this year' );
    $carbon_end   = new Carbon( 'last day of March next year' );
    $button['今年度']['start'] = $carbon_start->format('Y-m-d');
    $button['今年度']['end']   = $carbon_end  ->format('Y-m-d'); 
    $button['昨年度']['start'] = $carbon_start->subYears(1)->format('Y-m-d');
    $button['昨年度']['end']   = $carbon_end  ->subYears(1)->format('Y-m-d');

}

$carbon_start = new Carbon( 'first day of last month' );
$carbon_end   = new Carbon( 'last day of last month' );
$button['先月']['start'] = $carbon_start->format('Y-m-d');
$button['先月']['end']   = $carbon_end  ->format('Y-m-d'); 

$carbon_start = new Carbon( 'first day of this month' );
$carbon_end   = new Carbon( 'last day of this month' );
$button['今月']['start'] = $carbon_start->format('Y-m-d');
$button['今月']['end']   = $carbon_end  ->format('Y-m-d');                                    

$carbon_start = new Carbon( 'first day of next month' );
$carbon_end   = new Carbon( 'last day of next month' );
$button['来月']['start'] = $carbon_start->format('Y-m-d');
$button['来月']['end']   = $carbon_end  ->format('Y-m-d'); 
                        
$carbon_start = new Carbon( 'today' );
$button['今日']['start'] = $carbon_start->format('Y-m-d');
$button['今日']['end']   = $carbon_start->format('Y-m-d'); 

$carbon_start = new Carbon( 'tomorrow' );
$button['明日以降']['start'] = $carbon_start->format('Y-m-d');
$button['明日以降']['end']   ="2100-12-31";

@endphp  

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'SearchQuery', 1 ) }}
    @csrf
    
    <div class="container border border-primary bg-light shadow align-middle p-3">
        <div class="row">
            <div class="col-12 d-none d-lg-block">
                <div class="row" >
                    <div class="col-3 m-2">名前</div>
                    <div class="col-3 m-2">部署</div>
                    <div class="col-2 m-2">役職</div>
                    <div class="col-1 m-2">表示数</div>
                </div>
            </div>
            <div class="row container">
                @php
                    if( optional( auth( 'user' ))->user() ) { 
                        $auth = auth('user')->user(); 
                    } elseif( optional(auth('admin'))->user() ) {
                        $auth = auth('admin')->user(); 
                    } else {
                        abort( 403, 'parts_find.blade: 認証エラー' );
                    }
                    $grades= User::getArrayForGradeSelcetForm();
                @endphp
            
                <div class="col-3 d-lg-none m-2">名前</div>
                @if( $auth->is_user() and $auth->browsing() == "自分のみ" ) 
                    {{ Form::hidden( 'find[user_id]', $auth->id ) }} <div class="col-7 col-lg-3">本人のみ検索可能</div>
                @else
                    {{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                                    ['class' => 'form-control col-7 col-lg-3 m-2', 'placeholder' => '名前' ] ) }}
                @endif
            
                <div class="col-3 d-lg-none m-2">部署</div>

                @if( $auth->is_user() and $auth->browsing() == "自分のみ" ) 
                    <div class="col-7 col-lg-3">本人のみ検索可能</div>
                @else
                    @php
                        if( $auth->is_admin() or ( $auth->is_user() and $auth->browsing() == "全社" )) {
                            $depts = Dept::getArrayforSelect();
                        } else {
                            //　部内のみ検索可能
                            // 
                            $depts = Dept::getArrayforSelect( [ 'id' => $auth->department->id ], TRUE );
                        }
                    @endphp
                    {{ Form::select( 'find[dept_id]', $depts, old( 'find[dept_id]', ( isset( $find['dept_id'] )) ? $find['dept_id'] : null ),
                                        ['class' => 'form-control col-7 col-lg-3 m-2' ] ) }}
                @endif

                <div class="col-3 col-lg-2 d-lg-none m-2">役職</div>
                {{ Form::select( 'find[grade]', $grades, old( 'find[grade]', ( isset( $find['grade'] )) ? $find['grade'] : null ),
                            ['class' => 'form-control col-7 col-lg-2 m-2' ] ) }}

                <div class="col-3 col-lg-1 d-lg-none m-2">表示数</div>
                {{ Form::select( 'find[pagination]', [ 3=> 3, 10 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100 ] ,
                            old( 'find[pagination]', ( isset( $find['pagination'] )) ? $find['pagination'] : 30  ),
                                ['class' => 'form-control col-3 col-lg-1 m-2' ] )  }}
            </div>

        
            <div class="col-12">&nbsp;</div>

            <div class="col-12 col-lg-6 container b-1 p-1">
                <div class='row'>
                    <div class="col-12 col-lg-12">休暇年月日</div>
                    <div class="col-12">
                        <div class="container">
                            <div class="row">
                                {{ Form::date( 'find[start_date]', 
                                            old( 'find[start_date]', ( isset( $find['start_date'] )) ? $find['start_date'] : "" ), 
                                                    [ 'class' => 'col-5 form-control shadow w-30' , 'id' => 'start_date' ] ) }}
                                <div class='col-1'>～</div>
                                {{ Form::date( 'find[end_date]', 
                                            old( 'find[end_date]', ( isset( $find['end_date'] )) ? $find['end_date'] : "" ), 
                                                [ 'class' => 'col-5 form-control shadow w-30', 'id' => 'end_date' ] ) }}
                            </div>
                        </div>
                        <div class="container m-2">

                            @foreach( $button as $key => $date ) 
                                <a class="col-2 col-lg-3 m-1 btn btn-sm btn-outline btn-outline-dark date_button" data-start='{{ $date['start'] }}' data-end='{{ $date['end'] }}'>{{ $key }}</a>
                            @endforeach
                        </div>
                        <script>
                            $('.date_button').click( function(){
                                $('#start_date').val( $(this).data('start') ); 
                                $('#end_date').val( $(this).data('end') ); 
                            });
                        </script>   
                    </div>
                </div>
            </div>

        @php
            $status = [ '承認待ち'=>'承認待ち', '承認'=>'承認', '休暇取得完了' => '休暇取得完了' ];
            if( empty( $find['status'] )) { $find['status'] = array(); }
            $i=0;
        @endphp

        <div class="col-12 d-lg-none"></div>
        <div class='col-12 col-lg-3'>
            <div class="row">
                <div class="col-3 col-lg-12 m-2">ステータス</div>
                <div class="col-7 col-lg-12 m-2 container">
                    <div class="row">
                        @foreach( $status as $s )
                            <div class="col-12">
                            @php 
                                ( in_array( $s, $find['status'] )) ? $checked = true : $checked = false
                            @endphp 
                                {{ Form::checkbox( "find[status][$i]", $s, $checked, [ 'class' => 'form-check-input shadow'] ) }}
                                <div class='form-check-label'>{{ $s }}</div>
                            @php $i++ @endphp
                            </div>
                        @endforeach
                                
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col-12 d-lg-none"></div>
            <div class="col-12 col-lg-3">
                <div class="row container">
                    @if( preg_match( '/common\.vindex$/', Route::currentRouteName() ))
                        <div class="col-3 col-lg-12 m-2">休暇種別</div>
                        {{ Form::select( 'find[type]', array_merge( [ '' => '' ], config( 'vacation.constant.application.type' )),
                                     old( 'find[type]', ( isset( $find['type'] )) ? $find['type'] : null ), 
                                     [ 'class' => 'form-control col-7 col-lg-12 m-2' ] )  }}
                    @endif

                    @if( preg_match( '/common\.no_vacation$/', Route::currentRouteName()) ) 
                        <div class='form-check row'>
                            <div class="col-12">
                                {{ Form::checkbox( 'find[no_paid_leave]', 1, ( ! empty( $find['no_paid_leave'] )) ? 'checked' : '', [ 'class' => 'm-2' ] ) }}
                                有給未取得
                            </div><div class="col-12">
                                <!--{{ Form::checkbox( 'find[no_officer]', 1, ( ! empty( $find['no_officer'] )) ? 'checked' : '', [ 'class' => 'm-2' ] ) }}-->
                                <!--役員を除く-->
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
            
        <div class="col-12 container">
            <div class="row">
                <button class="btn btn-primary">検索</button>
            </div>
        </div>
    </div>
    
    @if( preg_match( '/common\.vindex$/', Route::currentRouteName()) )
        <div class="col-12 container m-2">
            <div class="row w-95 border border-dark p-2 m-1">
                <div class="col-12">表示内容</div>
                    @php
                        $show_items = [ '社員番号', '部署', '役職', '休暇種別',  '休暇理由', 'ステータス' ];
                        # dump( $find, $show_items );
                    @endphp
                    @foreach( $show_items as $item ) 
                        @php
                            ( ! empty( $find['show_item'] ) && in_array( $item, $find['show_item'], true )) ? $checked = 'checked' : $checked = "" ;
                        @endphp
                        <div class="col-12 col-lg-2">
                            <div class="row p-lg-1">
                                {{ Form::checkbox( "find[show_item][$item]", $item, $checked, [ 'class' => 'w-30 mtl-1' ] ) }}
                                <div class="w-70 mtl-1">{{ $item }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
{{ Form::close() }}
<div class="m-3"></div>

@php

@endphp 

