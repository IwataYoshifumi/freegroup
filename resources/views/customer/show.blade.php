@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Customer;

# dump( $customer->all() );

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'customer.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'customer.show_button' )
                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <form method="POST" action="{{ route('customer.store') }}">
                        @csrf

                        @include( 'customer.show_parts' )
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">

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
