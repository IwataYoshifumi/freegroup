@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\MyForm;

$labels = config( 'mail_order.columns_name' );
$types  = [ 'email' => 'email', 'delivery_date' => 'date', 'memo' => 'textarea', 'address' => 'text' ];
$widths = [ 'person' => '30', 
            'tel'     => '50',
            'fax'    => '50',
            'delivery_date' => '70',
            'delivery_tel'  => '70',
            'delivery_person' => '50',
            'delivery_postcode' => '30',
            'delivery_prefecture' => '30',
            'delivery_city' => '30',
        

            
            
            ];
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

                    {{ Form::open( [ 'method' => 'POST', 'route' => 'mail_order.store' ] ) }}
                        @csrf

                        @include( 'mail_order.order_form' )
                
                        <div class="card-body">
                            <div class="w-100">お客様情報</div>
                            <div class="container m-1">
                                {{ MyForm::input( [ 'names'     => config( 'mail_order.columns' ), 
                                                    'labels'    => $labels, 
                                                    #'values'    => $input,
                                                    'types'     => $types, 
                                                    'breakpoint'=> 'lg', 
                                                    'widths'    => $widths,
                                                    'confirms'  => $confirms,
                                                    ] ) }}
                            </div>
                            <div class="container m-1">
                                <div class="w-100">納品先</div>
                                {{ MyForm::input( [ 'names'     => config( 'mail_order.columns_delivery' ), 
                                                    'labels'    => $labels, 
                                                    'types'     => $types,
                                                    'widths'    => $widths,
                                                    'breakpoint'=> 'lg', 
                                                    ] ) }}
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">登録</button>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@php 
#dump( request()->all() );
@endphp
@endsection
