@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ScheduleType;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\View\groupware_models_customer_input_customers;

// dump( $schedule );

//　初期化
//
$customers = old( 'customers', optional( $schedule )->customers ); 
$users     = old( 'users',     optional( $schedule )->users ); 
$user      = User::find( $schedule->user_id );

$attached_files = old( 'attached_files', optional( $schedule->files )->toArray() );

#dd( $schedule->user_id, $user );
#dump( $attached_files, old( 'attached_files' ) );

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.schedule.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url'     => route( Route::currentRouteName(), [ 'schedule' => optional($schedule)->id ] ), 
                                      "enctype"=>"multipart/form-data", 
                                    ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $schedule )->id ) }}
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 col-form-label text-md-right">作成者</label>
                            <div class="col-md-8">
                                {{ Form::hidden( 'user_id', optional( $schedule )->user_id ) }}
                                {{ $user->p_dept_name() }} {{ $user->name }} {{ $user->grade }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $schedule )->name ) }}" autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
                            <div class="col-md-8">
                                <input type="text" name="place" value="{{ old( 'place', optional( $schedule )->place ) }}" class="form-control  @error('place') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">予定種別</label>
                            <div class="col-md-8">
                                @php
                                    $array = ScheduleType::get_array_for_select( auth( 'user' )->id() );
                                @endphp
                                {{ Form::select( 'schedule_type_id', $array, optional($schedule)->schedule_type_id, [ 'class' => 'col-4 form-control' ] ) }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8">
                                <input id="start_time" type="datetime-local" name="start_time" value="{{ old('start_time', optional( $schedule )->o_start_time() ) }}" 
                                    class="form-control w-75 @error('start_time') is-invalid @enderror" step='300'>
                                <input id="end_time"   type="datetime-local" name="end_time"   value="{{ old('end_time',   optional( $schedule )->o_end_time()   ) }}" 
                                    class="form-control w-75 @error('end_time') is-invalid @enderror" step='300'>
                                <div class="col-12"></div>
                                <div class="row m-1 w-100">
                                @php
                                    $periods = [ '時間', '終日', '複数日' ];
                                @endphp
                                @foreach( $periods as $p ) 
                                    <div class="col m-1">
                                        {{ Form::radio( 'period', $p, ( optional( $schedule )->period == $p ) ? 1 : 0, [ 'class' => '' ] ) }} 
                                        <div class="">{{ $p }}</div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="customers" class="col-md-4 col-form-label text-md-right">関連顧客</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <x-input_customers :customers="$customers"/>
                            </div>
                        </div>
                            
                        <div class="form-group row">
                            <label for="users" class="col-md-4 col-form-label text-md-right">関連社員</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <x-input_users :users="$users"/>
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                <textarea name="memo" value="{{ old( 'memo', optional( $schedule )->memo ) }}" class="form-control @error('memo') is-invalid @enderror"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">添付ファイル</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputFilesComponent --->                                
                                <x-input_files :attached_files="$attached_files" />
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            @if( preg_match( '/schedule.create$/', Route::currentRouteName() ))
                                <button type="submit" class="btn btn-primary">新規作成</button>
                            @elseif( preg_match( '/schedule.edit$/', Route::currentRouteName() ))
                                <button type="submit" class="btn btn-warning">　変更実行　</button>
                            @endif
                            {{ BackButton::form() }}

                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
