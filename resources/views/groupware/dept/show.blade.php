@extends('layouts.app')

@php

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

use App\myHttp\GroupWare\Models\Dept;

$users = $dept->users->where( 'retired', false );;

$url_update = route( 'dept.edit',    [ 'dept' => $dept->id ] );
$url_delete = route( 'dept.destory', [ 'dept' => $dept->id ] );

$disabled = ( ! op( admin() )->can( 'delete', $dept ) ) ? "disabled" : "";



#dump( $model_1 );

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.dept.menu' )
            
            <div class="card">
                <div class="card-header"><B>{{ $dept->name }}</B></div>
                
                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )

                <div class="card-body">
                    
                    @can( 'update', $dept )
                        <a class="btn btn-warning m-1"                 href="{{ $url_update }}">部署名変更</a>
                        <a class="btn btn-danger  m-1 {{ $disabled }}" href="{{ $url_delete }}">部署削除</a>
                    @endcan
                    
                    <table class='table table-hover'>
                        <tr class='bg-light'>
                            <th>職級</th>
                            <th>社員名</th>
                        </tr>
                        @foreach( $users as $user ) 
                            <tr>
                                <td>{{ $user->grade }}</td>
                                <td>{{ $user->name  }}</td>
                            </tr>
                        @endforeach
                        <tr class='bg-light'>
                            <th>社員数</th>
                            <th>{{ $users->count() }}　名</th>
                        </tr>
                    </table>

                {{ BackButton::form() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
