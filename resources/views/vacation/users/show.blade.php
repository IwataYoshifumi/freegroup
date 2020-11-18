@php

use App\Http\Helpers\BackButton;

@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include( 'vacation.users.menu' )
            <div class="card">
                <div class="card-header">従業員情報</div>


                
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )



                <div class="card-body col-sm-12">
                    <div class="border border-dark rounded">
                        <h6 class="bg-success text-white w-100 p-2">社員情報 UserID {{ $user->id }}</h6>
                        <div class="row m-1">
                            
                            @if( 0 && $user->is_admin() )
                                <div class="col-10 m-3 p-2 bg-danger text-white align-middle font-weight-bold font-color">管理者</div>
                                <div class="w-100"></div>
                            @endif
                            
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">部署名</div>
                            <div class="col-sm-7 m-1 bg-light align-middle">{{ $user->department->name }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">職級</div>
                            <div class="col-sm-7 m-1 bg-light align-middle">{{ $user->grade }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">名前</div>
                            <div class="col-sm-7 m-1 bg-light align-middlee">{{ $user->name }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">メールアドレス</div>
                            <div class="col-sm-7 m-1 bg-light align-middle text-nowrap">{{ $user->email }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">入社年月日</div>
                            <div class="col-sm-7 m-1 bg-light align-middle text-nowrap">{{ $user->join_date }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">新卒・中途</div>
                            <div class="col-sm-7 m-1 bg-light align-middle text-nowrap">{{ $user->carrier }}</div>
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">閲覧権限</div>
                            <div class="col-sm-7 m-1 bg-light align-middle text-nowrap">{{ $user->browsing }}</div>
                            @if( $user->retired ) 
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-warning align-middle font-weight-bold">退社年月日</div>
                            <div class="col-sm-7 m-1 bg-warning align-middle text-nowrap">{{ $user->retired_date }}</div>
                            @endif
                            
                            
                            <div class="w-100"></div>
                            <div class="col-sm-3 m-1 bg-light align-middle font-weight-bold">備考</div>
                            <div class="col-sm-7 m-1 bg-light align-middle">{{ $user->memo }}</div>
                        </div>
                    </div>
                    <div class="col-12 m-3">
                        {{ BackButton::form() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php
##dd( $applications );
@endphp

@endsection
