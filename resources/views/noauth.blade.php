@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            <div class="card">
                <div class="card-header">未承認ページアクセスエラー</div>
                
                <div class="card-body">
                    
                    <div class="alert alert-danger">未承認ページへアクセスしました</div>
                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    

                    <div class="form-group row mb-0">
                        <div class="col-12 m-1">
                            {{ BackButton::form() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
