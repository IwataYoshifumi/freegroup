@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Models\Admin;


@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'admin.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @include( 'admin.show_button' )
                    
                    <form method="POST" action="{{ route('admin.store') }}">
                        @csrf

                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['name'] }}</label>
                            <div class="col-md-6">{{ $admin->name }}</div>
                        </div>

                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['email'] }}</label>
                            <div class="col-md-6">{{ $admin->email }}</div>
                        </div>

                        @if( $admin->retired ) 
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">退社</label>
                                <div class="col-md-6">退社済み</div>
                            </div>
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'admin.columns_name' )['date_of_retired'] }}</label>
                                <div class="col-md-6">{{ $admin->date_of_retired }}</div>
                            </div>
                        @endif


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
