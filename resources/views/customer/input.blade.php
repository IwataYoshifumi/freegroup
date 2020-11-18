@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Customer;

if( ! isset( $customer ) ) { $customer = null; }

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'customer.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <form method="POST" action="{{ route( Route::currentRouteName(), [ 'customer' => optional( $customer )->id ] ) }}">
                        @csrf
                        {{ Form::hidden( 'id', optional( $customer )->id ) }}

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">名前</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $customer )->name ) }}" required autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kana" class="col-md-4 col-form-label text-md-right">ヨミカナ</label>
                            <div class="col-md-8">
                                <input type="text" name="kana" value="{{ old( 'kana', optional( $customer )->kana ) }}" class="form-control  @error('kana') is-invalid @enderror">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">メールアドレス</label>
                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email', optional( $customer )->email ) }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="zip_code" class="col-md-4 col-form-label text-md-right">郵便番号</label>
                            <div class="col-md-4">
                                    <input type="text" name="zip_code" value="{{ old( 'zip_code', optional( $customer )->zip_code ) }}"  class="form-control @error('zip_code') is-invalid @enderror">
                            </div>
                            <a class="btn btn-outline btn-outline-dark ml-1 col-2" onClick='input_address();'>住所入力</a>
                        </div>
                      	<script>
                            function input_address() {
                                console.log( 
                                    // AjaxZip3.VERSION,
                                    // AjaxZip3.PREFMAP,
                                    // AjaxZip3.JSONDATA,
                                    AjaxZip3.zip2addr( 'zip_code', '', 'prefecture', 'city', 'street', '' ),
                                );
                            }
                      	</script>

                        <div class="form-group row">
                            <label for="prefecture" class="col-md-4 col-form-label text-md-right">都道府県</label>
                            <div class="col-md-6">
                                <input type="text" name="prefecture" value="{{ old( 'prefecture', optional( $customer )->prefecture ) }}" class="form-control col-5 @error('prefecture') is-invalid @enderror" id="prefecture">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="city" class="col-md-4 col-form-label text-md-right">市区町村</label>
                            <div class="col-md-6">
                                <input type="text" name="city" value="{{ old( 'city', optional( $customer )->city ) }}" class="form-control @error('city') is-invalid @enderror" id="city">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="street" class="col-md-4 col-form-label text-md-right">町名</label>
                            <div class="col-md-6">
                                <input type="text" name="street" value="{{ old( 'street', optional( $customer )->street ) }}" class="form-control @error('street') is-invalid @enderror" id="street">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="building" class="col-md-4 col-form-label text-md-right">建物名・部屋番号</label>
                            <div class="col-md-6">
                                <input type="text" name="building" value="{{ old( 'building', optional( $customer )->building ) }}" class="form-control @error('building') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="tel" class="col-md-4 col-form-label text-md-right">電話番号（固定電話）</label>
                            <div class="col-md-5">
                                <input type="text" name="tel" value="{{ old( 'tel', optional( $customer )->tel ) }}" class="form-control @error('tel') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="fax" class="col-md-4 col-form-label text-md-right">FAX番号</label>
                            <div class="col-md-5">
                                <input type="text" name="fax" value="{{ old( 'fax', optional( $customer )->fax ) }}" class="form-control @error('fax') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">携帯電話</label>
                            <div class="col-md-5">
                                <input type="text" name="mobile" value="{{ old( 'mobile', optional( $customer )->mobile ) }}" class="form-control @error('mobile') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="birth_day" class="col-md-4 col-form-label text-md-right">誕生日</label>
                            <div class="col-md-6">
                                <input type="date" name="birth_day" value="{{ old( 'birth_day', optional( $customer )->birth_day ) }}" class="form-control @error('birth_day') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="birth_day" class="col-md-4 col-form-label text-md-right">性別</label>
                            <div class="col-md-5">
                                {{ Form::select( 'sex', [ '' => '', '男' => '男', '女' => '女' ], old( 'sex', optional( $customer )->sex ),  [ 'class' => 'form-control col-4' ]  ) }}
                            </div>
                        </div>
                        
                        @if( config( 'customer.salseforce.enable' )) 
                            <div class="form-group row">
                                <label for="birth_day" class="col-12 col-md-4 col-form-label text-md-right">セールスフォースＩＤ</label>
                                <div class="col-md-5">
                                    <input type="text" name="salseforce_id" value="{{ old( 'salseforce_id', optional( $customer )->salseforce_id ) }}" class="form-control">
                                </div>
                            </div>
                        @endif
                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
                            <div class="col-md-8">
                                <textarea name="memo" value="{{ old( 'memo', optional( $customer )->memo ) }}" class="form-control @error('memo') is-invalid @enderror"></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            @if( Route::currentRouteName() == "customer.create" ) 
                                <button type="submit" class="btn btn-primary">新規作成</button>
                            @elseif( Route::currentRouteName() == "customer.edit" )
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
