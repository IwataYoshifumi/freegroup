@php

use Carbon\Carbon;

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;


if( ! isset( $customer ) ) { $customer = null; }

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
                    {{ Form::open( [ 'url' => route( Route::currentRouteName() ) ]) }}
                        @method( 'POST' )
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name' ) }}" autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
                            <div class="col-md-8">
                                <input type="text" name="place" value="{{ old( 'place' ) }}" class="form-control  @error('place') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8">
                                <input id="start_time" type="datetime-local" name="start_time" value="{{ old('start_time', optional($defaults)['start_time']  ) }}" class="form-control w-75 @error('start_time') is-invalid @enderror">
                                <input id="end_time"   type="datetime-local" name="end_time"   value="{{ old('end_time'  , optional($defaults)['end_time']    ) }}" class="form-control w-75 @error('end_time') is-invalid @enderror">
                                <div class="col-12"></div>
                                <div class="row m-1 w-100">
                                @php
                                    $periods = [ '時間', '終日', '複数日' ];
                                @endphp
                                @foreach( $periods as $p ) 
                                    <div class="col m-1">
                                        {{ Form::radio( 'period', $p, ( $defaults['period'] == $p ) ? 1 : 0, [ 'class' => '' ] ) }} 
                                        <div class="">{{ $p }}</div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                <textarea name="memo" value="{{ old( 'memo' ) }}" class="form-control @error('memo') is-invalid @enderror"></textarea>
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
