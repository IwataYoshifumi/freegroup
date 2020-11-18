@extends('layouts.app')

@php
use App\Http\Controllers\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Vacation;

@endphp

@section('content')
<div class="container">
    <div class="col-lg-11">
        <div class="card">
            <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
            <div class="card-body">

                @include( 'layouts.flash_message' )
                @include( 'layouts.error' )
                
                <div class="col-12 clearfix ">
                    <div class="container float-left">
                    </div>
                </div>
                
                {{ Form::open( [ 'url' => route(Route::currentRouteName()), 
                                 'method' => 'get', 
                                 'class' => 'bg-light container w-90 clearfix p-3 m-1 border border-round border-round-dark',
                                 ] ) }}
                    <div class="row">
                        <div class="col-12 col-lg-3 m-1">休暇完了日：</div>
                        {{  Form::date( 'find[end_date]', old( 'find[end_date]', $find['end_date'] ), [ 'class' => 'form-control col-8 col-lg-4 m-1' ] ) }}
                        <div class="col-12"></div>
                        <div class="col-12 col-lg-3 m-1">申請ステータス：</div>
                        <div class="col-12 col-lg-7 m-1">承認待ち、承認済み（完了処理待ち）</div>
                        <div class="col-12"></div>
                        <button type=submit class="btn btn-primary m-2 col-3">検索</button>
                    </div>
                {{ Form::close() }}
                {{ Form::open( [ 'url' => route( 'vacation.application.notifyIncompleted'),
                                 'id'  => 'notify_incompleted',
                                 'method' => 'get',
                                ] ) }}
                    {{ Form::hidden( 'find[end_date]', $find['end_date'] ) }}
                {{ Form::close() }}
                
                <div class='container table table-border bg-light m-1 w-100'>
                            
                    <div class="row bg-dark text-white">
                        
                        <div class="col d-none d-lg-block">確認</div>
                        <div class="col d-none d-lg-block">部署</div>
                        <div class="col d-none d-lg-block">名前</div>
                        <div class="col d-none d-lg-block">ステータス</div>
                        <div class="col d-none d-lg-block">休暇種別</div>
                        <div class="col d-none d-lg-block">申請日</div>
                        <div class="col d-none d-lg-block">休暇終了日</div>
                        <div class="col d-none d-lg-block">休暇日数</div>
                    </div>
                    
                    @foreach( $applications as $app )
                        <div class="row w-100 mt-1">
                            <div class="col-6 col-lg">
                                <a class="btn btn-sm btn-outline btn-outline-secondary" 
                                    href="{{ route( 'vacation.application.show', [ 'application' => $app->id ] )}}">詳細</a>
                            </div>
                            <div class="col-6 col-lg">{{ $app->user->department->name  }}</div>
                            <div class="col-6 col-lg">{{ $app->user->name  }}</div>
                            <div class="col-6 col-lg">{{ $app->status }}</div>
                            <div class="col-6 col-lg">{{ $app->type   }}</div>
                            <div class="col-8 col-lg">{{ $app->date   }}</div>
                            <div class="col-12 col-lg">{{ $app->end_date }}</div>
                            <div class="col-12 col-lg">{{ Vacation::pnum( $app->num ) }}</div>
                            <div class="col-12 d-none-lg">&nbsp;</div>
                        </div>
                    @endforeach
                    @if( $applications->count() )
                        <div>
                            <button type=button class="btn btn-success m-2 col-3 text-white" onClick="onClickNotifyButton()">完了処理の催促</button>
                            <script>
                                function onClickNotifyButton() {
                                    $('#notify_incompleted').submit();
                                }
                            </script>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@php

@endphp 

@endsection