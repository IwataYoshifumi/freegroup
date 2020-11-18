@extends('layouts.app')

@php

use App\Models\Vacation\Dept;
use App\Models\Vacation\User;

use App\Http\Helpers\BackButton;

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-10">
            <div class="col-12">
                @include( 'vacation.approvalMaster.menu' )
            </div>
            <div class="card">
                <div class="card-header">新規　申請マスター作成</div>
                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <form method="POST" action="{{ route('vacation.approvalMaster.store') }}">
                        @csrf

                        <div class='col m-3'>
                        <div class="form-group row">
                            <label for="dept_id" class="col-md-3 col-form-label text-md-right">マスター名</label>

                            <div class="col-md-9">
                                {{ Form::text( 'name', old( 'name' ), ['class' => 'form-control required col-8' ] ) }}
                                
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">備考</label>

                            <div class="col-md-9">
                                {{ Form::text( 'memo', old( 'memo' ), [ 'class' => 'form-control' ] ) }}
                            </div>
                        </div>
                        
                        
                        <div class="border border-dark rounded mt-3">
                        <h6 class="bg-light text-black w-100 p-2">承認　申請先</h6>
                        @error('check_approver')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        @php
                            $depts = Dept::getArrayforSelect();
                        
                        @endphp

                        @for( $i = 0; $i <= 2; $i ++ ) 
                            <div class="form-group row">
                                <label for="type" class="col-md-3 col-form-label text-md-right">申請先{{ $i+1 }}</label>
                                <div class="col-md-3">
                                    {{ Form::select( 'dept'.$i, $depts, old( 'dept'.$i ), ['class' => 'form-control dept_select', 'data-id' => $i ] ) }}

                                </div>
                                <div class="col-md-4">
                                    {{ Form::select( 'approvers['.$i.']', array(), "" , [ 'class' => 'form-control approver_select', 'id' => "approver".$i, 'data-id' => $i ]) }}
                                    {{ Form::hidden( 'approver'.$i,  old( 'approver['.$i.']' ), [ 'data-id' => $i, 'id' => 'approver_hidden'.$i ] ) }}
                                </div>
                                <div id="r"></div>
                            </div>
                        @endfor
                        {{ Form::hidden( 'check_approver', old( 'check_approver' ), [ 'id' => 'check_approver' ] ) }}
                        
                        <script>

                            $('.dept_select').change( function() {
                                var i = $(this).data('id');
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
                                    $("#approver_hidden"+i).val( '' );

                                    //console.log( approver_id );
                                    // $('#applover_select'+i).val( approver_id );
                                    
                                }); 
                            });
                            

                            $('.approver_select').change( function (){
                               var i = $(this).data('id');
                               $("#approver_hidden"+i).val( $(this).val() );
                               
                               var num = 0;
                               $('.approver_select').each( function (){
                                   num += Number( $(this).val() );
                               })
                               $('#check_approver').val( num );
                            });



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
                        </div>
                        

                        <div class="form-group row mb-0">
                            <div class="col-12 offset-md-4 m-4">
                            <button type="submit" class="btn btn-primary">登録</button>
                            {{ BackButton::form() }}
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
