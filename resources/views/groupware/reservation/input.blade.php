@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Search\GetAccessLists;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\SubClass\GetFacilityForReservationInput;

// use App\myHttp\GroupWare\View\groupware_models_customer_input_customers;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;
use App\myHttp\GroupWare\View\Components\User\UsersCheckboxComponent;
use App\myHttp\GroupWare\View\Components\Customer\CustomersCheckboxComponent;

// if_debug( $reservation );

//　初期化
//
$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();
$creator   = ( $reservation->creator ) ? $reservation->creator : $auth;

if( $route_name == "groupware.reservation.edit" ) {
    $request->facilities = [ $reservation->facility_id ];
}

if( count( $request->facilities ) >= 1 ) {
    $facilities = ( isset( $request->facilities )) ? $request->facilities : [];
    $facilities = Facility::whereIn( 'id', $request->facilities )->get();
} else {
    $facilities = Facility::where( 'id', 0 )->get();
}

#dd( $creator->name, $updator );
#if_debug( $attached_files, old( 'attached_files' ) );

// エラー表示
$is_invalid['name'       ] = ( $errors->has( 'name'                ) ) ? 'is-invalid' : '';
$is_invalid['date_time'  ] = ( $errors->has( 'start_less_than_end' ) ) ? 'is-invalid' : '';
$is_invalid['facility_id'] = ( $errors->has( 'facility_id'         ) ) ? 'is-invalid' : '';

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9">

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => url()->full(),  "enctype" => "multipart/form-data", 'id' => 'input_form' ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $reservation )->id ) }}
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 col-form-label text-md-right">予約者</label>
                            <div class="col-md-7 m-1">
                                {{ $creator->dept->name }} {{ $creator->name }} {{ $creator->grade }}
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">予約設備</label>
                            <div class="col-md-8">
                                @foreach( $facilities as $facility )
                                    @if( $loop->first ) <div class="row m-1"> @endif
                                    @php
                                    #if( $facility->canWrite( $auth )) {
                                    if( $auth->can( 'reserve', $facility )) {
                                        $style = $facility->style();
                                    } else {
                                        $style = "";                        
                                    }
                                    @endphp

                                    <div class="col-12 text-truncate" style="{{ $style }}">{{ $facility->name     }}

                                        @if( $style )                                    
                                            <span class='uitooltip ml-2' title="{{ $facility->category }}( {{ $facility->sub_category }} )">@icon( info-circle )</span>
                                            {{ Form::hidden( 'facilities[]', $facility->id, [ 'class' => 'form-control', 'id' => 'facilities' ] ) }}
                                        @else
                                            {{ $facility->name }} は予約権限がないか、設備が無効化されているため予約不可
                                        @endif
                                    </div>
                                    
                                    @if( $loop->last ) </div> @endif
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">設備　利用目的</label>
                            <div class="col-md-8">
                                <input type="text" name="purpose" value="{{ old( 'purpose', optional( $reservation )->purpose ) }}" autocomplete="off" class="form-control @error('purpose') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8 container">
                                <div class="container row">

                                    @if( $route_name == "groupware.reservation.create" )
                                        @php
                                            $checked = ( $input->all_day ) ? 1 : 0;
                                            $time_span = [ 1 => '１分間隔', 5 => '５分間隔', 10 => '１０分間隔', 15 => '１５分間隔', 30 => '３０分間隔', 60 => '１時間間隔' ];
                                            $date_class = 'date_input form-control col-6 mb-1 '.$is_invalid['date_time'];
                                            $time_class = 'time_input form-control col-4 mb-1 '.$is_invalid['date_time'];
                                        @endphp
                                        {{ Form::text( 'start_date', old( 'start_date', $input->start_date ), [ 'class' => $date_class ,'autocomplete' => 'off', 'id' => 'start_date',  ] ) }}
                                        {{ Form::text( 'start_time', old( 'start_time', $input->start_time ), [ 'class' => $time_class ,'autocomplete' => 'off', 'id' => 'start_time' ] ) }}
                                        
                                        {{ Form::text( 'end_date',   old( 'end_date',   $input->end_date ),   [ 'class' => $date_class, 'autocomplete' => 'off', 'id' => 'end_date',  ] ) }}
                                        {{ Form::text( 'end_time',   old( 'end_time',   $input->end_time ),   [ 'class' => $time_class, 'autocomplete' => 'off', 'id' => 'end_time'  ] ) }}
                                        
                                        <div class="controlgroup">
                                            <label for='all_day'>終日</label>
                                            {{ Form::checkbox( 'all_day', 1, $input->all_day, [ 'class' => '', 'id' => 'all_day' ] ) }}
                                            
                                            {{ Form::select( 'time_span', $time_span, old( 'time_span', 30 ), [ 'class' => '', 'id' => 'time_span_select', 'onChange' => 'change()' ] ) }}
                                        </div>
                                    @else 
                                        <div class="col-10 mb-1">
                                            {{ $reservation->p_time() }} 
                                            <span class="icon_btn uitooltip" title="予約時間の変更はできません。予約をキャンセルして、再度設備予約してください">@icon( exclamation-circle )</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @push( 'timepicker_script' )
                            <script>
                            
                            
                                // 開始日時を変更したら、終了日時も変更するスクリプト
                                //
                                let d_span = 0; // 開始日と終了日の差分
                                let o_start_date = $('#start_date');
                                let o_end_date   = $('#end_date');
                                
                                function calulate_d_span() {
                                    console.log( 'before d_span', d_span );
                                    var start_date = new Date( o_start_date.val() );
                                    var end_date   = new Date( o_end_date.val() );
                                    d_span = end_date - start_date;
                                    console.log( 'after d_span', d_span );
                                }

                                $(document).ready( function() {
                                    calulate_d_span();
                                });
                                
                                o_end_date.on( 'change', function() { calulate_d_span(); });
                                
                                o_start_date.on( 'change', function() {
                                    var start_date = new Date( o_start_date.val() );
                                    var end_date   = new Date( start_date.getTime() + d_span );
                                    console.log( start_date.getDate(), end_date.getDate() );
                                    var month = end_date.getMonth() + 1;
                                    var date  = end_date.getDate();
                                    if( month < 10 ) { month = "0" + month; }
                                    if( date  < 10 ) { date  = "0" + date;  }
                                    
                                    var val = end_date.getFullYear() + "-" + month + "-" + date;
                                    o_end_date.val( val );
                                
                                });
                                
                                
                                //　開始時刻を変更したら、同じ時間間隔で終了時刻を自動変更するスクリプト
                                //
                                let t_span = 0; // 開始時刻と終了時刻の差分
                                
                                function calulate_t_span() {
                                
                                    var start_time = new Date( "2020-01-01T" + $('#start_time').val() + ":00" );
                                    var end_time   = new Date( "2020-01-01T" + $('#end_time'  ).val() + ":00" );
                                    var tmp = end_time.getTime() - start_time.getTime();
                                    if( ! isNaN( tmp )) { t_span = tmp; }
                                    
                                    console.log( t_span );
                                }

                                $('#end_time').on( 'change', function() {
                                    console.log( 'start_time.val()', $('#start_time').val() );
                                    console.log( 'end_time.val()', $('#end_time').val() );
                                    
                                    if( $('#end_time').val() == 0 ) { $('#end_time').val( '00:00' ); }
                                    console.log( 'end_time.val()', $('#end_time').val() );
                                    calulate_t_span();
                                });

                                
                                $(document).ready( function() {
                                    calulate_t_span();
                                });
                            
                                //　開始時刻を変更したら、終了時刻も更新
                                //
                                $('#start_time').on( 'change', function() {
                                    console.log( 'start_time.val()', $(this).val() );
                                    
                                    var start_time_val = $(this).val();
                                    if( start_time_val == 0 ) { // 00:00 を選ぶと数字の０が入りエラーになるための対策 
                                        start_time_val = "00:00"; 
                                        $(this).val( "00:00" ); 
                                    }
                                    console.log( 'start_time.val()', $(this).val(), t_span );

                                    var t = "2020-01-01T" + start_time_val + ":00";
                                    var start_time = new Date( t );
                                    var end_time = new Date( start_time.getTime() + t_span );
                                    var minutes = end_time.getMinutes();
                                    if( minutes < 10 ) { minutes = "0" + minutes; }
                                    
                                    $('#end_time').val( end_time.getHours() + ':' + minutes );
                                    
                                    console.log( t, start_time.toString(), end_time.toString() );
                                    
                                });
                                


                                //　時刻間隔セレクトフォーム
                                //
                                function time_span() {
                                    var span = $('#time_span_select').val();
                                    console.log( span );
                                    $('.time_input').timepicker( 'option', 'step', span );
                                }
                                
                                $(document).ready( function() {
                                
                                
                                    var options = {
                                        timeFormat: 'H:i',
                                        // defaultDate: moment('2015-01-01'),
                                        useCurrent:'day'
                                    };
                                
                                   $('.time_input').timepicker( options );
                                   // $('.time_input').timepicker( 'option', 'timeFormat', 'H:i' );

                                   var start_date = $('#start_date').val();
                                   var end_date   = $('#end_date').val();
                                   $('.date_input').datepicker({ 
                                            scrollDefault: 'now',
                                            //showOtherMonths: true,
                                            selectOtherMonths: true,
                                            numberOfMonths: 2,
                                            showButtonPanel: true,
                                   });
                                   
                                   
                                   $('.date_input').datepicker( 'option', 'dateFormat', 'yy-mm-dd' );
                                   //$('.date_input').datepicker( 'option', $.datepicker.regional[ 'ja' ] );
                                   $('#start_date').datepicker( 'setDate', start_date );
                                   $('#end_date').datepicker(   'setDate', end_date   );
                                   
                                   $( ".controlgroup" ).controlgroup()
                                   $('#all_day').checkboxradio();
                                   
                                });
                                
                                $('#time_span_select').selectmenu( {
                                    change: function( event, ui ) {
                                        var span = $(this).val();
                                        console.log( span, event, ui );
                                        console.log( ui.label, event.type );
                                        $('.time_input').timepicker( 'option', 'step', span );
                                    }
                                });

                            </script>
                        @endpush
                    
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                {{ Form::textarea( 'memo', op( $reservation )->memo, [ 'class' => 'form-control' ] ) }}
                            </div>
                        </div>
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                @php
                                    if( $route_name == 'groupware.reservation.create' ) {
                                        $btn_class = 'btn-primary text-white';
                                        $btn_title = '設備予約　作成';
                                    } elseif( $route_name = 'groupware.reservation.edit' ) {
                                        $btn_class = 'btn-warning';
                                        $btn_title = '設備予約　変更';
                                    }
                                @endphp
                                <a class="btn {{ $btn_class }}" onClick="$('#input_form').submit()">{{ $btn_title }}</a>

                            {{ BackButton::form() }}

                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stack( 'timepicker_script' )


@endsection
