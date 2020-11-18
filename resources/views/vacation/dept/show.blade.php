@extends('layouts.app')

@php

use App\Models\Vacation\Dept;

$users = $dept->users->where( 'retired', false );;

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'vacation.dept.menu' )
            
            <div class="card">
                <div class="card-header"><B>{{ $dept->name }}</B></div>
                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )

                <div class="card-body">
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

                <a href='{{ route( 'vacation.dept.index' ) }}' class='btn btn-outline-secondary'>戻る</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
