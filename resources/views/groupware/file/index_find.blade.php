@php

use Carbon\Carbon;

use App\Models\Dept;
use App\myHttp\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

$today = new Carbon( 'today' );

$button['今年']['start'] = Carbon::parse( 'first day of January' )->format('Y-m-d');
$button['今年']['end']   = Carbon::parse( 'last day of December'  )->format('Y-m-d'); 

$button['先月']['start'] = Carbon::parse( 'first day of last month' )->format('Y-m-d');
$button['先月']['end']   = Carbon::parse( 'last day of last month'  )->format('Y-m-d');

$button['今月']['start'] = Carbon::parse( 'first day of this month' )->format('Y-m-d');
$button['今月']['end']   = Carbon::parse( 'last day of this month' )->format('Y-m-d');                                    

$button['先週']['start'] = $today->copy()->startOfWeek()->subDays(7)->format('Y-m-d');
$button['先週']['end']   = $today->copy()->endOfWeek()->subDays(7)->format('Y-m-d');                         

$button['今週']['start'] = $today->copy()->startOfWeek()->format('Y-m-d');
$button['今週']['end']   = $today->copy()->endOfWeek()->format('Y-m-d');

$button['今日']['start'] = $today->format('Y-m-d');
$button['今日']['end']   = $today->format('Y-m-d'); 

@endphp

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'find', 1 ) }}
    @csrf

    <div class="container border border-dark p-1 w-95 m-1 p-1">
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-8 d-none d-lg-block p-1">アップロード期間</div>
            <div class="col-4 d-none d-lg-block p-1">表示数</div>
        </div>
        <div class="row p-1 container m-1">
            <div class="col-12 d-lg-none p-1">アップロード期間</div>
            <div class="col-lg-8 clearfix">
                <div class="row p-1">
                {{ Form::date( 'start_date', op( $request )->start_date, ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'start_date' ] ) }}
                <div class="col-1 m-1">～</div>
                {{ Form::date( 'end_date',   op( $request )->end_date,   ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'end_date'   ] ) }}
                </div>
                <div class="col-lg-12">
                    @foreach( $button as $key => $date ) 
                        <a class="col-3 col-lg-1 m-1 btn btn-sm btn-outline btn-outline-dark date_button" data-start='{{ $date['start'] }}' data-end='{{ $date['end'] }}'>{{ $key }}</a>
                    @endforeach
                </div>
                <script>
                    $('.date_button').click( function(){
                        $('#start_date').val( $(this).data('start') ); 
                        $('#end_date').val( $(this).data('end') ); 
                    });
                </script>  
            </div>
            
            <div class="col-12 d-lg-none p-1">表示数</div>
            <div class="col-lg-4 clearfix">
                {{ Form::select( 'pagination', config( 'constant.pagination' ), op( $request )->pagination, [ 'class' => 'form-control' ] ) }}
            </div>
            
        </div>
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-4 d-none d-lg-block p-1">ファイル名</div>
            <div class="col-4 d-none d-lg-block p-1">添付有無</div>
            <div class="col-4 d-none d-lg-block p-1">アップロード社員</div>
        </div>
        <div class="row p-1 container m-1">
            <div class="col-12 d-lg-none p-1">ファイル名</div>
            <div class="col-lg-4 p-1 clearfix">
                {{ Form::text( 'file_name', op( $request )->file_name , [ 'class' => 'form-control' ] ) }}
            </div>

            <div class="col-12 d-lg-none p-1">添付有無</div>
            <div class="col-lg-4 p-1 clearfix">
                @php
                    $attached = [ '' => '',  1 => '添付あり', -1 => '添付なし' ];
                @endphp
                {{ Form::select( 'attached', $attached, op( $request )->attached , [ 'class' => 'form-control col-6' ] ) }}
            </div>

            <div class="col-12 d-lg-none p-1">アップロード者 @if(is_debug()) <i class="fab fa-dev icon_debug"></i> @endif </div>
            <div class="col-lg-4 p-1 clearfix">
                @if( is_debug() )
                    @php
                        $users = ( isset( $request->users )) ? $request->users : [];
                    @endphp
                    
                    <x-input_users :users="$users"/>
                @else 
                    <input type='hidden' name='users[]' value="{{ user_id() }}">
                    自分のアップロードファイルが検索対象
                @endif
            </div>
        </div>

        <div class="col-12 container">
            <div class="row">
                <button class="btn btn-search col-6 col-lg-3 m-1 p-1">検索</button>
            </div>
        </div>
    </div>
{{ Form::close() }}