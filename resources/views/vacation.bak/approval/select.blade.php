@extends('layouts.app')

@php
    use App\Application;
    use App\User;
    use App\Dept;

@endphp

@section('content')
<div class="container">

    @include( 'vacation.approval.menu' ) 
            
    <div class="card">
        <div class="card-header bg-primary text-wight-bold text-white font-size-5">休暇承認業務</div>
                
        @include( 'layouts.error' )
        @include( 'layouts.flash_message' )

        <div class="card-body">
            <div class="border border-dark rounded d-none d-md-block">
                <h6 class="bg-primary text-white w-100 p-2">承認者( User {{ $user->id }} / Auth {{ Auth::id() }} ）</h6>
                <div class="row m-1">
                    <div class="col-sm-3 m-1 bg-light align-middle">{{ $user->department->name }}</div>
                    <div class="col-sm-2 m-1 bg-light align-middle">{{ $user->grade }}</div>
                    <div class="col-sm-4 m-1 bg-light align-middle">{{ $user->name }}</div>
                </div>
            </div>

            <div class="border border-dark rounded mt-3">
                <h6 class="bg-primary text-white w-100 p-2">休暇申請一覧</h6>
                <div class='m-1'>
                            
                    <div class="m-1 w-95">
                        {{ Form::open( [ 'url' => route( 'vacation.approval.select', 
                                                            [ 'user'    => $user ] ,[ 'class'   => 'form-control'] ) ] ) }}
                            @csrf
                            @method('GET')

                            <label>申請者</label>
                            {{ Form::text( 'find[aluser_name]',  $find['aluser_name'] )  }}
                            {{ Form::submit( '検索', [ 'class' => 'm-1' ] ) }}
                        {{ Form::close() }}
                    </div>
                    
                        
                    @include( 'vacation.approval.select_parts', [ 'approvals' => $approvals ] );  

                </div>
            </div>
        </div>
    </div>
</div>
@php


@endphp 
@endsection

