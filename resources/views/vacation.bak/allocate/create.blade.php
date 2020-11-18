@extends('layouts.app')

@php
use Carbon\Carbon;

use App\Models\Vacation\Vacation;
use App\Models\Vacation\VacationList;

use App\Http\Helpers\BackButton;

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include( 'vacation.allocate.menu' )
            <div class="card">
                <div class="card-header bg-orange">{{ config( Route::currentRouteName() ) }}</div>

                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )
                
                <div class="card-body">
                    {{ Form::open( ['url' => route( 'vacation.allocate.store' ), 'method' => 'post', 'id' => 'main_form' ] ) }}
                    @csrf
                    
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white w-100 p-2">有給割当　情報</div>

                            <div class='form-group row'>
                                <label class="col-sm-2 m-2 ml-3">割当年度</label>
                                <div class="col-sm-6 m-2">
                                {{ Form::number( 'year', old( 'year' ) , [ 'class' => 'form-control', 'id' => 'year', 'required' ]) }}
                                @error( 'year' )
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                
                                
                                        @php
                                            $carbon = new Carbon( 'last year' );
                                            $last_year = $carbon->year;
                                            $carbon = new Carbon( 'this year' );
                                            $this_year = $carbon->year;
                                            $carbon = new Carbon( 'next year');
                                            $next_year = $carbon->year;

                                        @endphp
                                        
                                        
                                        
                                    <div class="row m-2 align-center">
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary year_btn" data-date='{{ $last_year }}'>{{ $last_year }}年度</a>
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary year_btn" data-date='{{ $this_year }}'>{{ $this_year }}年度</a>
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary year_btn" data-date='{{ $next_year }}'>{{ $next_year }}年度</a>
                                    </div>
                                    <script>
                                        $('.year_btn').click( function(){
                                            $('#year').val( $(this).data('date')); 
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class='form-group row'>
                                <label class="col-sm-2 m-2 ml-3">割当日</label>
                                <div class="col-sm-6 m-2">
                                {{ Form::date( 'allocate_date', old( 'allocate_date' ) , [ 'class' => 'form-control', 'id' => 'allocate_date', 'required' ]) }}
                                        @php
                                            $carbon     = new Carbon( 'last year' );
                                            $lastApril  = $carbon->format('Y-04-01');
                                            $carbon     = new Carbon( 'today' );
                                            $thisApril  = $carbon->format('Y-04-01');
                                            $carbon     = new Carbon( 'next year' );
                                            $nextApril  = $carbon->format('Y-04-01');

                                        @endphp
                                    <div class="row m-2 align-center">
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary allocate_btn" data-date="{{ $lastApril }}">{{ $lastApril }}</a>
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary allocate_btn" data-date="{{ $thisApril }}">{{ $thisApril }}</a>
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary allocate_btn" data-date="{{ $nextApril }}">{{ $nextApril }}</a>
                                    </div>
                                    <script>
                                        $('.allocate_btn').click( function(){
                                            $('#allocate_date').val( $(this).data('date') ); 
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class='form-group row'>
                                <label class="col-sm-2 m-2 ml-3">有効期限</label>
                                <div class="col-sm-6 m-2">
                                {{ Form::date( 'expire_date', old( 'expire_date' ), [ 'class' => 'form-control', 'id' => 'expire_date', 'required' ] ) }}
                                    @error('expire_date')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    @php
                                        $carbon = new Carbon('last day of March next year');
                                        $carbon->addYear(1);                    
                                        $last_date_of_march = $carbon->format('Y-m-d');
                                    @endphp
                                    <div class="row m-2 align-center">
                                        <a class="col-3 m-1 btn btn-sm btn-outline-secondary expire_btn" data-date='{{ $last_date_of_march }}'>2年後年度末</a>
                                    </div>
                                    <script>
                                        $('.expire_btn').click( function(){
                                            $('#expire_date').val( $(this).data('date')); 
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 m-2 ml-3">有給日数</label>
                                <div class="col-sm-6 m-2">
                                {{ Form::number( 'num', old( 'paid_leave' ), [ 'class' => 'form-control', 'id' => 'paidleave', 'required' ] ) }}
                                    <div class="row m-2">
                                        <div class="row align-center">
                                            <a class="col m-1 btn btn-sm btn-outline-secondary paidleave_btn" data-date=10>10日</a>
                                            <a class="col m-1 btn btn-sm btn-outline-secondary paidleave_btn" data-date=12>12日</a>
                                            <a class="col m-1 btn btn-sm btn-outline-secondary paidleave_btn" data-date=13>13日</a>
                                            <a class="col m-1 btn btn-sm btn-outline-secondary paidleave_btn" data-date=15>15日</a>
                                        </div>
                                        <script>
                                            $('.paidleave_btn').click( function(){
                                                $('#paidleave').val( $(this).data('date')); 
                                            });
                                            
                                            
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-9">
                                    <button type='submit' class="btn btn-primary submit-btn text-white m-3">有給割当</button>
                                    {{ BackButton::form() }}
                                </div>
                            </div>
                            
                        </div>
                        




                    <div class="card mt-3">
                        <div class="card-header bg-success text-white w-100 p-2">割当従業員一覧</div>
                        <div class="table table-bordered m-2">
                            <div class="row thead-dark m-2">
                                <div class="col m-1 ">部署</div>
                                <div class="col m-1">役職</div>
                                <div class="col m-1">名前</div>
                                <div class="col m-1">入社年月日</div>
                            </div>
                            @php $num = 0; @endphp
                            @foreach( $users as $user ) 
                                @php $num++ @endphp
                                <div class="row">
                                    {{ Form::hidden( "users[$num]", $user->id  ) }}
                                    <div class="col m-1">
                                        {{ optional( $user->department )->name }}</div>
                                    <div class="col m-1">{{ $user->grade            }}</div>
                                    <div class="col m-1">{{ $user->name             }}</div>
                                    <div class="col m-1">{{ $user->join_date        }}</div>
                                </div>
                            @endforeach
                                <div class="row thead-dark">
                                    <div class='col-12 text-left m-2'>合計：{{ $num }} 名</div>
                                </div>
                                <div class="col-12 m-2">
                                    {{ BackButton::form() }}
                                </div>
                        </div>
                    </div>


                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@php
    //dd( $users );
    //dd( $request );
@endphp 

@endsection

