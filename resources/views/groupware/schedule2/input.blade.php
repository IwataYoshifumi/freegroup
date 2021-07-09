@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Search\GetAccessLists;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\SubClass\GetCalendarForScheduleInput;

// use App\myHttp\GroupWare\View\groupware_models_customer_input_customers;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;
use App\myHttp\GroupWare\View\Components\User\UsersCheckboxComponent;
use App\myHttp\GroupWare\View\Components\Customer\CustomersCheckboxComponent;

// if_debug( $schedule );

//　初期化
//
$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();
$creator   = ( $schedule->creator ) ? $schedule->creator : $auth;
$updator   = ( $schedule->updator ) ? $schedule->updator : null;
$customers = old( 'customers', optional( $schedule )->customers ); 
$users     = old( 'users',     optional( $schedule )->users ); 
$attached_files = old( 'attached_files', optional( $schedule->files )->toArray() );

$permissions = Schedule::getPermissions();

//　予定追加可能なカレンダー( not_use, disable は対象外）
//
//$calendars = toArrayWithEmpty( Calendar::all(), 'name', 'id' );

//　カレンダーの変更の変更はカレンダー作成者のみ可能
//
if( $route_name == 'groupware.schedule.create' ) {
    $calendars = toArrayWithEmpty( GetCalendarForScheduleInput::user( $auth->id ), 'name', 'id' );
    $input_calendar_enable = true;
    
} elseif( $route_name == 'groupware.schedule.edit' and $schedule->user_id == $auth->id ) {
    $calendar = $schedule->calendar;
    $calendars = toArrayWithEmpty( GetCalendarForScheduleInput::getFromUserAndCalendar( $auth->id, $calendar ), 'name', 'id' );
    $input_calendar_enable = true;
    
} else {
    $input_calendar_enable = false;
}

//　変更権限を自動的にカレンダーのデフォルト値に設定するためのデータ
//
if( isset( $calendars )) {
    $Calendars = Calendar::whereIn( 'id', array_keys( $calendars ) )->get();
}


#dd( $creator->name, $updator );
#if_debug( $attached_files, old( 'attached_files' ) );

// エラー表示
$is_invalid['name'       ] = ( $errors->has( 'name'                ) ) ? 'is-invalid' : '';
$is_invalid['date_time'  ] = ( $errors->has( 'start_less_than_end' ) ) ? 'is-invalid' : '';
$is_invalid['calendar_id'] = ( $errors->has( 'calendar_id'         ) ) ? 'is-invalid' : '';

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9">

            @include( 'groupware.schedule2.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => url()->full(),  "enctype" => "multipart/form-data", 'id' => 'input_form' ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $schedule )->id ) }}
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 col-form-label text-md-right">作成者・更新者</label>
                            <div class="col-md-7 m-1">
                                {{ $creator->dept->name }} {{ $creator->name }} {{ $creator->grade }}
                                @if( $creator->id != op( $updator )->id and $updator )
                                    更新者：{{ $updator->dept->name }}{{ $updator->name }}
                                @endif
                                
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $schedule )->name ) }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
                            <div class="col-md-8">
                                <input type="text" name="place" value="{{ old( 'place', optional( $schedule )->place ) }}" autocomplete="off" class="form-control  @error('place') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">カレンダー</label>
                            <div class="col-md-8">

                                @if( $input_calendar_enable ) 
                                    {{ Form::select( 'calendar_id', $calendars, $schedule->calendar_id, [ 'class' => 'form-control', 'id' => 'calendar_form' ] ) }}
                                    <script>
                                        //　カレンダーを切り替えたときに、変更権限を自動的に設定する
                                        //
                                        var calendar_form = $('#calendar_form');
                                        var permission  = $('#permission' );
                                        var permissions = [];
                                        @foreach( $Calendars as $calendar )
                                            permissions[{{ $calendar->id }}] = "{{ $calendar->default_permission }}";
                                        @endforeach
                                        
                                        calendar_form.on( 'change', function() {
                                            var calendar_id = $(this).val();
                                            var permission = permissions[ calendar_id ];
                                            console.log( calendar_id, permission );
                                            $('#default_permission').val( permission );
                                        });
                                    </script>
                                    
                                @else 
                                    {{ $schedule->calendar->name }}
                                    {{ Form::hidden( 'calendar_id', $schedule->calendar_id ) }}
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8 container">
                                <div class="container row">
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
                    
                        @if( $route_name == 'groupware.schedule.create' or 
                           ( $route_name == 'groupware.schedule.edit' and $creator->id == $auth->id ))
                            <div class="form-group row">
                                <label for="place" class="col-md-4 col-form-label text-md-right">変更権限</label>
                                <div class="col-md-8">
                                    {{ Form::select( 'permission', $permissions, $schedule->permission, [ 'class' => 'form-control col-6', 'id' => 'default_permission' ] ) }}
                                </div>
                            </div>
                        @endif
                    
                        
                        <div class="form-group row">
                            <label for="customers" class="col-md-4 col-form-label text-md-right">関連顧客</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <!--x-input_customers :customers="$customers"/>-->
                                <x-checkboxes_customers :customers="$customers" button="顧客検索" />
                                
                            </div>
                        </div>
                            
                        <div class="form-group row">
                            <label for="users" class="col-md-4 col-form-label text-md-right">関連社員</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <!--x-input_users :users="$users"/>-->
                                <x-checkboxes_users :users="$users" button="社員検索" />
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                {{ Form::textarea( 'memo', op( $schedule )->memo, [ 'class' => 'form-control' ] ) }}
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">添付ファイル</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputFilesComponent --->                                
                                {{--<x-input_files :attached_files="$attached_files" />--}}
                                
                                <x-input_files2 :input="$component_input_files" />
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                @php
                                    if( $route_name == 'groupware.schedule.create' ) {
                                        $btn_class = 'btn-primary text-white';
                                        $btn_title = '新規スケジュール作成';
                                    } elseif( $route_name = 'groupware.schedule.edit' ) {
                                        $btn_class = 'btn-warning';
                                        $btn_title = 'スケジュール変更';
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
