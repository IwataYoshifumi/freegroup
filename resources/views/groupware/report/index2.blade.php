@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

#dump( Request::all() );
#dump( session( 'back_button' ) );

$route_name = Route::currentRouteName();

$auth = auth( 'user' )->user();

$reports = $returns['reports'];
$report_props = $returns['report_props'];
$report_lists = $returns['report_lists'];

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.report.find_form' )
                    
                    <!-- 一覧フォーム -->
                    @php
                        $columns_name = [ '', '作成日時', '作成者', '日報リスト名', '件名', ];
                    @endphp
                    
                    <div class="m-1 p-1 border clearfix">
                        <div class="row">
                            @foreach( $columns_name as $name ) 
                                <div class="col">{{ $name }}</div>
                            @endforeach
                        </div>
                        
                        @foreach( $reports as $report )
                            @php
                                $route_to_show =  route( 'groupware.report.show', [ 'report' => $report->id ] );
                                $disabled = ( $auth->can( 'view', $report )) ? "" : "disabled";
                                $report_list = $report->report_list;
                                $report_prop = $report_props[$report->report_list_id];
                                
                            @endphp
                        
                            <div class="row">
                                <div class="col">
                                    <a class="btn btn-sm btn-outline-secondary {{ $disabled }}" href="{{ $route_to_show }}">詳細</a>
                                </div>
                                <div class="col">{{ $report->created_at->format( 'Y-m-d H:i' ) }}</div>
                                <div class="col">{{ op( $report->user )->name     }}</div>
                                <div class="col" style='{{ $report_prop->style() }}'>{{ $report_prop->name }}
                                    @if( $report_list->name != $report_prop->name )
                                        <span style="color: gray" title='管理者設定名：{{ $report_list->name }}'>@icon( info-circle )</span>

                                    @endif

                                
                                </div>
                                <div class="col">{{ $report->name           }}</div>
                            </div>
                        @endforeach
                        @if( method_exists( $reports, 'links' )) 
                            <div class="col mt-2">
                                {{ $reports->appends( $request->all() )->links() }}
                                
                            </div>
                        @endif
                    </div>

                    <div class="w-100"></div>
                    @if( count( $reports )) 
                        {{ OutputCSV::button( [ 'route_name' => 'groupware.report.csv', 'inputs' => $request->all() , 'method' => 'GET' ]) }}
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

