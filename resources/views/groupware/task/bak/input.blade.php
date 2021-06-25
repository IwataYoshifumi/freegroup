@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;



//　関連顧客の変数初期化
//
$customers = old( 'customers', optional( $report )->customers ); 

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'report' => optional($report)->id ] ) ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $report )->id ) }}
                    
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 col-form-label text-md-right">作成者</label>
                            <div class="col-md-8">
                                {{ Form::hidden( 'user_id', optional( $report )->user_id ) }}
                                {{ optional( $report->user)->name }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $report )->name ) }}" autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
                            <div class="col-md-8">
                                <input type="text" name="place" value="{{ old( 'place', optional( $report )->place ) }}" class="form-control  @error('place') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8">
                                <input id="start_time" type="datetime-local" name="start_time" value="{{ old('start_time', optional( $report )->o_start_time() ) }}" class="form-control w-75 @error('start_time') is-invalid @enderror">
                                <input id="end_time"   type="datetime-local" name="end_time"   value="{{ old('end_time',   optional( $report )->o_end_time()   ) }}"   class="form-control w-75 @error('end_time') is-invalid @enderror">
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
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                <textarea name="memo" value="{{ old( 'memo', optional( $report )->memo ) }}" class="form-control @error('memo') is-invalid @enderror"></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            @if( preg_match( '/report.create$/', Route::currentRouteName() ))
                                <button type="submit" class="btn btn-primary">新規作成</button>
                            @elseif( preg_match( '/report.edit$/', Route::currentRouteName() ))
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
