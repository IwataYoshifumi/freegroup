@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Admin;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Depts;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;
use App\myHttp\GroupWare\View\Components\User\UsersCheckboxComponent;
use App\myHttp\GroupWare\View\Components\Customer\CustomersCheckboxComponent;

$route_name = Route::currentRouteName();

$depts     = ( op( $request )->depts     ) ? $request->depts     : [ 1,2 ];
$users     = ( op( $request )->users     ) ? $request->users     : [ 1,2 ];
$customers = ( op( $request )->customers ) ? $request->customers : [ 1,2 ];

$options = [ 'form_name' => 'departments', 'button_label' => '部署を検索する' ];

if_debug( request()->all() );
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

                        {{--
                        x-checkboxes_depts :depts="$depts" :options="$options" />
                        x-checkboxes_depts :depts="$depts" button="部署を検索する" name="{{ $options['form_name'] }}"  />
                        --}}
                        
                        {{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET' ] ) }}
                            @method( 'GET' )
                            @csrf
                            <x-checkboxes_depts :depts="$depts" button="部署検索す" />
                            <x-checkboxes_users :users="$users" button="社員検索す" formclass="col-2" />

                            <x-checkboxes_customers :customers="$customers" button="顧客を探す" formclass="col-2" />
                            
                            <div class="col-12"></div>
                            <button type="submit" class="btn btn-success col-4 m-1">Submit</button>
                        {{ Form::close() }}
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

