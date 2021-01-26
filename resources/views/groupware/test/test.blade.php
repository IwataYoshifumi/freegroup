@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

#use App\Models\User;
#use App\Models\Admin;
#use App\Models\Customer;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Admin;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Depts;

$route_name = Route::currentRouteName();

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ $route_name }}</div>
                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    <div class="container border border-dark">
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

