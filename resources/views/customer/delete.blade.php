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
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    
                    <div class="alert alert-warning h5 flush">
                    @if( Route::currentRouteName() == "customer.delete" )
                        下記の顧客データを削除する場合は、「削除実行」ボタンを押してください。関連データも削除されます。
                    @elseif( Route::currentRouteName() == "customer.deleted" ) 
                        下記の顧客データを削除しました。関連データも削除されました。
                    @endif
                    </div>
                    
                    <form method="POST" action="{{ route('customer.delete', ['customer' => $customer->id ]) }}" id="delete_form">
                        @method( "DELETE" )
                        @csrf

                        @include( 'customer.show_parts' )
                        
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if( Route::currentRouteName() == "customer.delete" )
                                    <a class="btn btn-danger text-white" onClick="submit_button()">削除実行</a>
                                    
                                @endif
                                {{ BackButton::form() }}

                            </div>
                            <script>
                                function submit_button() {
                                    $('#delete_form').submit();
                                }
                                
                            </script>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
