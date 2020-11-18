@extends('layouts.app')

@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Http\Helpers\BackButton;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @php
                        $columns_name = [ '', 'ファイル名', '所有者', 'アップロード日時', '添付' ];
                        $route_name = Route::currentRouteName();
                    @endphp
                    
                    @if( $route_name == "groupware.file.delete" )
                        <div class="alert alert-danger">ファイルを削除します。この操作は取り消しできません。よろしければ「削除実行」ボタンを押してください。</div>
                    
                    @endif
                    

                    <!-- 一覧フォーム -->
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
                        
                        {{ Form::open( [ 'route' => $route_name, 'name' => 'delete_form' ] ) }}
                            @method( 'DELETE' )
                    
                            @foreach( $files as $file )
                                <div class="col-12 border border-light"></div>
                                <div class="row mt-1">
                                    @php
                                        if( count( $file->schedules ) or count( $file->reports ) ) {
                                            $attached = "有";
                                        } else {
                                            $attached = "";
                                        }
                                        $route_show = route( 'groupware.file.show', [ 'file' => $file->id ] );
                                    @endphp
                                    
                                    <div class="col-12 col-lg-1 d-none d-lg-block">
                                        {{ Form::hidden( 'files[]', $file->id ) }}
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
                            @if( $route_name == 'groupware.file.delete' )
                                <a class="btn btn-danger text-white" onClick="document.delete_form.submit()">ファイル削除実行</a>
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

