@extends('layouts.app')

@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use Illuminate\Support\Facades\Auth; 

@endphp  


@section('content')
<div class="container">
    <div class="container">
        @include( 'vacation.common.menu' )
        
    </div>
    
    <div class="card">
        <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
        <div class="card-body">
            <div>
                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )
            </div>
            @include( 'vacation.common.parts_find', [ 'find' => $find ] )

            <table class="table table-bordered table-hover p-1">
                <tr>
                    <th>&nbsp;</th>
                    <th>部署</th>
                    <th>役職</th>
                    <th>名前</th>
                    <th>休暇日数</th>
                </tr>
                @if( ! is_null( $vacation )) 
                    @foreach( $vacation as $v ) 
                        <tr>
                            <td>
                                <a class='btn btn-success' href='{{ route( 'vacation.user.detail', [ 'user' => $v->id ] ) }}'>詳細</a>
                            </td>
                            <td>{{ $v->dept  }}</td>
                            <td>{{ $v->grade }}</td>
                            <td>{{ $v->name  }}</td>
                            <td>@if( isset( $find['no_paid_leave'] )) 
                                    有給未申請・未取得
                                @else 
                                    {{ $v->num  }}日 
                                @endif 
                            </td>
                        </tr>
                    @endforeach
                @endif
            </table>
            @if( ! is_null( $vacation ))
                <div class="m1">
                    {{ $vacation->appends( [ 'find' => $find, 'SearchQuery' => 1] )->links() }}
                </div>
            @endif

        </div>
    </div>
</div>
@php


@endphp 

@endsection

