@php
use App\myHttp\Schedule\Models\ScheduleType;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\BackButton;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.schedule_type.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    {{ Form::open( [ 'url' => route( 'groupware.schedule.type.update', [ 'schedule_type' => $schedule_type ] ), "enctype" => "multipart/form-data",  ] ) }}
                        @csrf
                        @method( 'POST' )
                        <div class="row">
                            <div class="col-3 m-1">スケジュール種別名</div>
                            {{ Form::text( 'input[name]', $schedule_type->name, [ 'class' => 'col-7 form-control m-1' ] ) }}
                            
                            <div class="col-3 m-1">背景色</div>
                            {{ Form::color( 'input[color]', $schedule_type->color, [ 'class' => 'col-1 form-control m-1', 'id' => 'color', 'onChange' => 'sample();' ] ) }}
                            <div class="col-2 p-1"><span id="sample1" class="m-2 p-1">色サンプル</span></div>
                            
                            <div class="col-12"></div>
                            
                            <div class="col-3 m-1">文字色</div>
                            {{ Form::color( 'input[text_color]', $schedule_type->text_color, [ 'class' => 'col-1 form-control m-1', 'id' => 'text-color', 'onChange' => 'sample()' ] ) }}
                
                            <script type="text/javascript">
                                function sample() {
                                    backgroud_color = $('#color').val();
                                    text_color = $('#text-color').val();
                                    console.log( backgroud_color, text_color );
                                    console.log( $('#sample1').css( 'background-color'));
                                    $('#sample1').css( 'background-color', backgroud_color );
                                    $('#sample1').css( 'color', text_color );
                                }

                                $(document).ready(function(){
                                    sample();
                                });                                
                                
                                
                            </script>            
                            
                            
                            <hr class="m-2">
                            
                            <div class="col-12">Googleカレンダーと同期する場合は、下記を入力してください。</div>
                            
                            <div class="col-3 m-1">Google カレンダーID</div>
                            {{ Form::text( 'input[google_calendar_id]', $schedule_type->google_calendar_id, [ 'class' => 'col-7 form-control m-1' ] ) }}
                            
                            <div class="col-3 m-1">Google サービスアカウントID</div>
                            {{ Form::email( 'input[google_id]', $schedule_type->google_id, [ 'class' => 'col-7 form-control m-1' ] ) }}
                            
                            <div class="col-3 m-1">Googleサービスアカウント秘密鍵</div>
                            {{ Form::file( 'google_private_key_file', [ 'class' => 'col-7 m-1' ] ) }}
                            <div class="col-3 m-1"></div>
                            <div class="col-6 m-1">
                                @foreach( $schedule_type->files as $file )
                                    {{ $file->file_name }}<br>
                                @endforeach
                            </div>
                            
                            <div>

                                <button type='button' class="btn btn-warning m-3" onClick="this.form.submit()">変更</button>
                                {{ BackButton::form() }}
                            </div>
                            
                        </div>
                    {{ Form::close() }}


                    <div class="w-100"></div>
        
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp



@endsection