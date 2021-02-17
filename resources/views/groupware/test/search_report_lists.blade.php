@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

$route_name = Route::currentRouteName();
$auth = auth( 'user' )->user();

#$report_lists = ReportList::getCanRead( $auth );
$report_lists = ReportList::where( 'id', 0 )->get();

if_debug( request()->all(), $report_lists );

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

                        {{ Form::open( [ 'route' => $route_name, 'method' => 'GET' ] ) }}
                            @csrf

                            <x-checkbox_report_lists :reportlists="$request->REPORT_LISTS" name='REPORT_LISTS' />


                            <button type="submit">サブミット</button>

                        {{ Form::close() }}

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

