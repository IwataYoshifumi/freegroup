@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

#use App\Models\Vacation\Dept;
use App\Models\Dept;

@endphp


@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            @include( 'dept.menu' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                <div class="card-body">

                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                    


                    <div class='table table-bordered table-hover col-12 col-lg-6 p-2'>
                        {{ Form::open( [ 'route' => Route::currentRouteName() ] ) }}
                            @csrf
                            @method( 'GET' )
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    {{ Form::text( 'find[name]', old( 'find[name]', $find['name'] ), [ 'class' => 'form-control', 'placeholder' => '部署名' ] ) }}
                                </div>
                                <div class="col-12 col-lg-6">
                                    <button type="submit" class="btn btn-search">検索</button>
                                </div>
    
                            </div>
                        {{ Form::close() }}
                    </div>

                    <table class='table table-bordered table-hover col-12 col-lg-6'>
                        <tr class='text-center thead-light'>
                            <th class="w-auto">部署名</th>
                            <th class="w-auto">アクション</th>
                        </tr>
                        
                        @foreach( $depts as $dept ) 

                            <tr class="text-left">
                                <td class="w-auto">{{ $dept->name }}</td>
                                <td class="">
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'dept.show',    $dept->id ) }}">所属社員</a>                                 
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'dept.edit',    $dept->id ) }}">変更</a>
                                    <a class="show-btn btn btn-sm btn-outline-danger text-danger"   href="{{ route( 'dept.destory', $dept->id ) }}">削除</a>
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

