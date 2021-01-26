@extends('layouts.app')

@php

if( ! isset( $customer ) ) { $customer = null; }

use App\Http\Helpers\BackButton;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Report;

use App\myHttp\GroupWare\Requests\SubRequests\ComfirmDeletionRequest;

if( ! isset( $customer ) ) { $customer = null; }

$customers = $report->customers;
$users     = $report->users;
$user      = $report->user;
$files     = $report->files;
$schedules   = $report->schedules;
$report_list  = $report->report_list;

$route_name = Route::currentRouteName();

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}( report_id {{ $report->id }} )</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @if( $route_name == "groupware.report.delete" and ! count( $errors ))
                        <div class="alert alert-danger">日報を削除します。よろしいですか。</div>
                    @elseif( $route_name == "groupware.report.deleted" ) 
                        <div class="alert alert-warning">日報を削除しました。</div>
                    @endif
                    
                    
                    {{ Form::open( [ 'url' => route( Route::currentRouteName(), [ 'report' => optional($report)->id,  ]), 'name' => 'delete_form' ] ) }}
                        @method( 'DELETE' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $report )->id ) }}
                        
                        @include( 'groupware.report.show_parts' )
                        
                        
                        <div class="col-12">

                            @if( $route_name == 'groupware.report.delete'  )
                                <div>
                                    <label for="comfirm_deletion">関連データも全て削除されます。この操作は取り消しできません。</label>
                                    <input type="checkbox" name="{{ ComfirmDeletionRequest::getInputName() }}" value=1 class="checkboxradio" id="comfirm_deletion">
                                </div>
                            
                                <a class="btn btn-danger text-white" onClick="document.delete_form.submit()">削除実行</a>
                            @endif
                            {{ BackButton::form() }}

                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
