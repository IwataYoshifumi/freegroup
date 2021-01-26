@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;

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
                        開発テスト用テンプレートBladeファイル
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        @if_debug
                            aaaaa
                        @else
                            bbbb
                            
                        @endif_debug
                        <div class="text-danger btn icon_btn m-1"> @icon( trash ) </div>
                        
                        <div class="text-primary btn icon_btn m-1" style="font-size: 40px"> @icon( search ) </div>
                        
                        <div class="text-success btn icon_btn m-1"> @icon( 'aaa' ) </div>
                        
                        @all_icons
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

