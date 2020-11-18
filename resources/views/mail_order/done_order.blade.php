@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\MyForm;

$labels = config( 'mail_order.columns_name' );
$types  = [ 'email' => 'email', 'delivery_date' => 'date', 'memo' => 'textarea', 'address' => 'text' ];
$confirms = [ 'email' => true ];

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                @include( 'mail_order.menu' )
                <div class="card">

                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                        <div>
                            ご注文ありがとうございました。
                            下記の通り、注文をウェブフォームから受付しました。
                            発送までしばらくお待ちください。
                            送料も含めた請求書をまたお送りします。
                        </div>
                        <div class="font-weight-bold">注文日時 {{ now() }}</div>

                        <div class="card-body">
                            <div class="w-100">お客様情報</div>
                            <div class="container m-1 border border-dark bg-light">
                                <div class="row">
                                    @foreach( config( 'mail_order.columns' ) as $column )
                                        <div class="col-4">{{ $labels[$column] }}</div>
                                        <div class="col-7">{{ Arr::get( $order, "input.$column" ) }}</div>
                                    @endforeach
                                </div>
                            </div>

                            </div>
                            <div class="container m-1">
                                <div class="w-100">納品先</div>
                                <div class="container m-1 border border-dark bg-light">
                                <div class="row">
                                    @foreach( config( 'mail_order.columns_delivery' ) as $column )
                                        <div class="col-4">{{ $labels[$column] }}</div>
                                        <div class="col-7">{{ Arr::get( $order, "input.$column" ) }}</div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="w-100">注文内容</div>  
                            <div class="container m-1 bg-light border border-dark">
                                <div class="row">
                                    <div class="col-5">品名</div>
                                    <div class="col-2">数量</div>
                                    <div class="col-2">単価</div>
                                    <div class="col-2">小計</div>
                                </div>
                                    @foreach( Arr::get( $order, 'item' ) as $i => $item )
                                        @if( ! empty( Arr::get( $order, "num.$i" )))    
                                            <div class="col-11"><hr></div>
                                            <div class="row mb-1">
                                                <div class="col-5">{{ $item }}</div>
                                                <div class="col-2">{{ Arr::get( $order, "num.$i"      )  }}</div>
                                                <div class="col-2">{{ Arr::get( $order, "price.$i"    )  }} 円</div>
                                                <div class="col-2">{{ Arr::get( $order, "subtotal.$i" )  }} 円</div>
                                            </div>
                                        @endif
                                    @endforeach
                                <div class="col-11"><hr></div>
                                <div class="row">
                                    <div class="col-9">小計</div>
                                    <div class="col-2">{{ Arr::get( $order, "all_subtotal" ) }} 円</div>
                                    <div class="col-9">消費税</div>
                                    <div class="col-2">{{ Arr::get( $order, "tax" ) }} 円</div>
                                    <div class="col-9">合計</div>
                                    <div class="col-2">{{ Arr::get( $order, "total" ) }} 円</div>
                                    
                                    
                                    
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php 
#dump( request()->all() );
@endphp
@endsection
