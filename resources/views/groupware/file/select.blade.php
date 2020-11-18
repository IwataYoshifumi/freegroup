@php
use App\myHttp\Schedule\Models\Schedule;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include( 'groupware.file.menu_button' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.file.index_find' )
                    
                    <!-- 一覧フォーム -->
                    @php
                        $columns_name = [ '', 'ファイル名', '所有者', 'アップロード日時', '添付' ];
                    @endphp
                    
                    <div class="m-1 p-1 border clearfix">
                        
                        <div class="d-none d-lg-block">
                            <div class="row container">
                                <div class="col-1">詳細</div>
                                <div class="col-4">ファイル名</div>
                                <div class="col-2">所有者</div>
                                <div class="col-3">アップロード日時</div>
                                <div class="col-1">添付</div>
                            </div>
                        </div>
                        
                        @foreach( $files as $file )
                            <div class="col-12 border border-light"></div>
                            <div class="row mt-1">
                                @php
                                    $route_show   = route( 'groupware.file.show',   [ 'file' => $file->id ] );
                                    $route_detail = route( 'groupware.file.detail', [ 'file' => $file->id ] );
                                    if( count( $file->schedules ) or count( $file->reports ) ) {
                                        $attached = "有";
                                    } else {
                                        $attached = "";
                                    }
                                @endphp
                                
                                <div class="col-12 col-lg-1 d-none d-lg-block">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ $route_detail }}">詳細</a>
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">ファイル名</div>
                                        <div class="col-7 col-lg-12">
                                            {{ $file->file_name     }}
                                            <a href="{{ $route_show }}"><i class="fas fa-search"></i></a> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">所有者</div>
                                        <div class="col-7 col-lg-12">{{ $file->user->name     }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-lg-3">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">アップ</div>
                                        <div class="col-7 col-lg-12">{{ $file->p_created_at() }}</div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-1">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">添付有無</div>
                                        <div class="col-7 col-lg-12">{{ $attached     }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="w-100">
                        @if( count( $files )) 
                            {{ $files->appends( [ 'find' => $find, 'pagination' => $request->pagination ] )->links() }}
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

