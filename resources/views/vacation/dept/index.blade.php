@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\Dept;


@endphp


@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'vacation.dept.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

                    <table class='table table-bordered table-hover w-50' >
                        <tr class='text-center thead-light'>
                            <th class="w-auto">部署名</th>
                            <th class="w-auto">アクション</th>
                        </tr>
                        
                        @foreach( $depts as $dept ) 

                            <tr class="text-left">
                                <td class="w-auto">{{ $dept->name }}</td>
                                <td class="">
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.dept.show',    $dept->id ) }}">所属社員</a>                                 
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.dept.edit',    $dept->id ) }}">変更</a>
                                    <a class="show-btn btn btn-sm btn-outline-danger text-danger"   href="{{ route( 'vacation.dept.destory', $dept->id ) }}">削除</a>
                                </td>
                            </tr>
                        @endforeach
                        

                    </table>
                    @php
                        
                    
                    @endphp
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

