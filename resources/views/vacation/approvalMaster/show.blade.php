@extends('layouts.app')

@php

use App\Models\Vacation\Dept;
use App\Models\Vacation\User;

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include( 'vacation.approvalMaster.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                <div class="card-body">

                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                        <div class='col'>
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-3 col-form-label text-md-right">マスター名</label>
                            <div class="col-md-9">{{ $master->name  }}</div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">備考</label>
                            <div class="col-md-9">{{ $master->memo }}</div>
                        </div>
                        
                        
                        <div class="border border-dark rounded mt-3">
                        <h6 class="bg-light text-black w-100 p-2">承認　申請先</h6>

                        @php
                            $depts = Dept::getArrayforSelect();
                            $approverList = $master->approvalMasterLists;
                            
                        @endphp
                        <div class="table table-border table-border-light">
                            @foreach( $approverList as $item )
                                <div class="row m-2">
                                <div class="col-3">{{ $item->approver->department->name }}</div>
                                <div class="col-5">{{ $item->approver->name }}</div>
                                </div>            
                            @endforeach
                        </div>
                        </div>

                        <div class="border border-dark rounded mt-3">
                        <h6 class="bg-light text-black w-100 p-2">マスター割当先</h6>

                        @php
                            $users = DB::table( 'users' )->join( 'approval_master_allocates as alloc', 'users.id', '=', 'alloc.user_id' )
                                                         ->join( 'depts', 'users.dept_id', '=', 'depts.id' )
                                                         ->select( 'users.name', 'users.grade', 'depts.name as dept_name' )
                                                         ->where( 'alloc.approval_master_id', '=', $master->id )
                                                         ->get();
                        @endphp
                        <div class="table table-border table-border-light">
                            @foreach( $users as $user )
                                <div class="row m-2">
                                <div class="col-3">{{ $user->dept_name }}</div>
                                <div class="col-3">{{ $user->grade     }}</div>
                                <div class="col-5">{{ $user->name      }}</div>
                                </div>            
                            @endforeach
                        </div>
                        </div>
                        <a class='btn btn-outline btn-dark m-2' href='{{ route( 'vacation.approvalMaster.index' ) }}'>一覧画面へ</a>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    //dd( $errors );                    
@endphp

@endsection

