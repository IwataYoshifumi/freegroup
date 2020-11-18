@extends('layouts.app')

@php
use App\Models\Vacation\Application;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include( 'vacation.application.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                

                <div class="card-body">
                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error'   )
                    
                    <form method="POST" action="{{ route('vacation.application.store' ) }}">
                        @csrf


                    <div class="border border-dark rounded d-none d-sm-block">
                        <h6 class="bg-light w-100 p-2">申請者</h6>
                        <div class="row m-1">
                            <div class="col-sm-3 m-1 align-middle">{{ $user->department->name }}</div>
                            <div class="col-sm-2 m-1 align-middle">{{ $user->grade }}</div>
                            <div class="col-sm-4 m-1 align-middle">{{ $user->name }}</div>
                        </div>
                        {{ Form::hidden( 'user_id', $user->id ) }}
                    </div>

                    <div class="border border-dark rounded mt-3">
                        <h6 class="bg-orange text-white w-100 p-2">申請内容</h6>

                        <div class="form-group row">
                            <label for="date" class="col-4 col-form-label text-md-right ml-2">申請日</label>

                            <div class="col-7">
                                <input id="date" type="hidden" class="form-control @error('data') is-invalid @enderror" 
                                        name="date" value="{{ old('date', $today ) }}" required>
                                <div class="form-control">{{ $today }}</div>
                                    
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="type" class="col-4 col-form-label text-md-right ml-2">休暇種別</label>
                            <div class="col-7">
                                {{ Form::select( "type", Application::getDayOffTypes(), old( 'type' ), 
                                                [ 'class' => 'form-control' ]
                                ) }}

                                @error('date')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="type" class="col-4 col-form-label text-md-right ml-2">有給割当</label>
                            <div class="col-7">
                                @php
                                    $array = Vacation::getArrayForSelectOfApplicationForm( $user );
                                @endphp
                                
                                {{ Form::select( "vacation_id", $array, old( 'vacation_id' ), 
                                                [ 'class' => 'form-control' ]
                                ) }}
                                @error('date')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror

                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="type" class="col-4 col-form-label text-md-right ml-2">休暇期間</label>
                            <div class="col-7">


                                <input id="start_date" type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                        name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                                <input id="end_date" type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                        name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror


                            </div>
                        </div>
                        
                        
                        <div class="form-group row">
                            <label for="type" class="col-4 col-form-label text-md-right ml-2">休暇日数</label>
                            <div class="col-7">
                                <input id="num" type="number" class="form-control @error('num') is-invalid @enderror" 
                                        name="num" value="{{ old('num') }}" required min='0'>

                                @error('num')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="reason" class="col-4 col-form-label text-md-right ml-2">休暇理由</label>
                            <div class="col-7">
                                <input id="reason" type="text" class="form-control @error('reason') is-invalid @enderror" 
                                        name="reason" value="{{ old('reason') }}" required>

                                @error('reason')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>
                
                    <div class="border border-dark rounded mt-3 clearfix">
                        <h6 class="bg-orange text-white w-100 p-2">承認　申請先</h6>

                        @error('check_approver')
                                <span class="invalid-feedback clearfix" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror

                        <!-- 承認マスターのデフォルト呼び出し関連  -->
                        
                        <!-- count : {{ $default_approvers->count() }} -->
                        
                        @if( $default_approvers->count() >= 1 ) 
                            @foreach( $default_approvers as $i => $approver ) 
                                <div id='default_approver{{ $i }}' class='default_approver' data-user_id='{{ $approver->user_id }}' data-dept_id='{{ $approver->dept_id }}'>
                                <!--    user_id : {{ $approver->user_id }} dept_id : {{ $approver->dept_id }}  -->
                                </div> 
                            @endforeach
                            <button type='button' class='btn btn-warning col-6 col-md-4 m-3' id='default_button'>申請先　初期設定</button>
                            <div class="col-12"></div>
                            
                            <script>
                                $('#default_button').click( function() {
                                    var i = 0;
                                    var dept_id = "";
                                    // console.log( $('#dept0').val() );
                                    // $('#dept0').val( 2 );
                                    $('.default_approver').each( function () { 
                                        dept_id = $(this).data( 'dept_id' );
                                        $('#approver_hidden'+i).val( $(this).data( 'user_id' ));
                                        $('#dept'+i).val( dept_id );
                                        dept_select( $('#dept'+i ) );
                                        i++;
                                    }); 
                                    // approver_check();

                                });                              
                                
                            </script>
                        @endif

                        @for( $i = 0; $i <= 2; $i ++ ) 

                            <div class="form-group">
                                <div class="container clearfix m-1 p-1 row">
                                    <div class="container clearfix col-11 col-md-3">
                                    <label for="type" class="w-100 form-label"><div class="text-md-center align-middle">申請先{{ $i+1 }}</div></label>
                                    </div>
                                    <div class="container clearfix col-11 col-md-7">
                                        {{ Form::select( 'dept'.$i, $depts, old( 'dept'.$i ), 
                                                [ 'id' => "dept".$i,  'class' => 'w-50 m-2 form-control dept_select', 'data-id' => $i ] ) }}
                                    
                                        {{ Form::select( 'approvers['.$i.']', array(), "" , [ 'class' => 'w-75 m-2 form-control approver_select', 'id' => "approver".$i, 'data-id' => $i ]) }}
                                        {{ Form::hidden( 'approver'.$i,  old( 'approver['.$i.']' ), [ 'data-id' => $i, 'id' => 'approver_hidden'.$i ] ) }}
                                    </div>
                                </div>
                            </div>
                        @endfor
                        {{ Form::hidden( 'check_approver', old( 'check_approver' ), [ 'id' => 'check_approver' ] ) }}
                        
                        <script>

                            //  部署セレクトを変更した時
                            //
                            $('.dept_select').change( function() {
                                dept_select( $(this) ); 
                            });
                            
                            //　部署を変更したら、社員セレクトを更新
                            //
                            function dept_select( obj ) {
                                console.log( obj.val() );
                                
                                var i = obj.data('id');
                                var url = "{{ route( 'vacation.json.getUsersBlongsTo' ) }}";
                                var documentOpened = $('#documentOpened').val();
                                $.ajax( url, {
                                    ttype: 'get',
                                    data: { dept_id : obj.val() },
                                    dataType: 'json',
                                }).done( function( data ) {
                                    $("#approver"+i).children().remove();
                                    $("#approver"+i).append($("<option>").val("").text("---"));
                                    var approver_id = $('#approver_hidden'+i).val();
                                    $("#approver_hidden"+i).val( "" );
                                    
                                    $.each( data, function( id, name ) {
                                        if( id == approver_id ) {
                                            $("#approver"+i).append($("<option>").val(id).text(name).prop("selected", true));
                                            $("#approver_hidden"+i).val( id );
                                            // console.log( id, name );
                                        } else {
                                            $("#approver"+i).append($("<option>").val(id).text(name));
                                        }
                                        approver_check();
                                    });
                                });
                            }

                            $('.approver_select').change( function (){
                                approver_select( $(this) );
                            });

                            function approver_select( obj ) {
                               var i = obj.data('id');
                               $("#approver_hidden"+i).val( obj.val() );
                                
                                approver_check();                               
                            }
                            
                            function approver_check( ) {
                               var num = 0;
                               $('.approver_select').each( function (){
                                   num += Number( $(this).val() );
                               })
                               $('#check_approver').val( num );
                            }
                            

                            $('.document').ready( function() {
                                $('.dept_select').each( function( i, element ) {
                                    // var i = $(this).data('id');
                                    var url = "{{ route( 'vacation.json.getUsersBlongsTo' ) }}";
                                    var documentOpened = $('#documentOpened').val();
                                    $.ajax( url, {
                                        ttype: 'get',
                                        data: { dept_id : $(this).val() },
                                        dataType: 'json',
                                    }).done( function( data ) {
                                        $("#approver"+i).children().remove();
                                        $("#approver"+i).append($("<option>").val("").text("---"));
                                        var approver_id = $('#approver_hidden'+i).val();
                                        $.each( data, function( id, name ) {
                                            if( id == approver_id ) {
                                                $("#approver"+i).append($("<option>").val(id).text(name).prop("selected", true));
                                                console.log( id, name );
                                            } else {
                                                $("#approver"+i).append($("<option>").val(id).text(name));
                                            }
                                        });
                                    });
                                });
                            });
                        </script>



                        <div class="form-group row mb-1">
                            <div class="col-md-3 offset-md-4">
                            <button type="submit" class="btn btn-success m-2">休暇申請</button>
                            </div>
                        </div>
                    </div>
                    
                </form>
                </div>

        </div>
    </div>
</div>
@php

@endphp 

@endsection

