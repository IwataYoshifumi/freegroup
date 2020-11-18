@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

if( ! isset( $customer ) ) { $customer = null; }

@endphp

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
                    <form method="POST" action="{{ route( Route::currentRouteName(), [ 'schedule' => optional( $schedule )->id ] ) }}">
                        @csrf
                        {{ Form::hidden( 'id', optional( $schedule )->id ) }}

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $schedule )->name ) }}" required autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kana" class="col-md-4 col-form-label text-md-right">ヨミカナ</label>
                            <div class="col-md-8">
                                <input type="text" name="kana" value="{{ old( 'kana', optional( $schedule )->kana ) }}" class="form-control  @error('kana') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            @if( Route::currentRouteName() == "schedule.create" ) 
                                <button type="submit" class="btn btn-primary">新規作成</button>
                            @elseif( Route::currentRouteName() == "schedule.edit" )
                                <button type="submit" class="btn btn-warning">　変更実行　</button>
                            @endif
                            {{ BackButton::form() }}

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
