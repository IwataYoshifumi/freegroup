@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;


$reservations_num = $facility->reservations()->count();

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.access_list.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        @if( $reservations_num )
                            <div class="alert-warning col-11">この設備には{{ $reservations_num }} 件の設備予約が登録されています。</div>
                            <div class="alert-warning col-11">設備を削除することで、これらの設備予約も全て削除されます。</div>
                        @else
                            <div class="alert-warning col-11">この設備に紐づけられている設備予約はありません。</div>
                        @endif

                        @include( 'groupware.facility.show_parts' )
                        
                        </div>

                        <div class="form-group row">
                            @if( Route::currentRouteName() == "groupware.facility.delete" )
                                <form id="delete_form" method="POST" action="{{ url()->current() }}" class="col-12">
                                    @method( 'DELETE' )
                                    @csrf
                                    <div class="row p-1">
                                        <div class="col-2"></div>
                                        <div class="col-9">
                                            <label for="delete_comfirm_1" class="font-weight-bold w-90 ">この設備に紐づけされている全ての設備予約も削除されます</label>
                                            {{ Form::checkbox( 'delete_comfirm[0]', 1, 0, [ 'class' => 'checkboxradio', 'id' => 'delete_comfirm_1' ] ) }} 
                                            
                                            <label for="delete_comfirm_2" class="font-weight-bold w-90 ">削除したデータは復帰できません。この操作は取り消しできません。</label>
                                            {{ Form::checkbox( 'delete_comfirm[1]', 1, 0, [ 'class' => 'checkboxradio', 'id' => 'delete_comfirm_2' ] ) }} 
                                        </div>
                                        <div class="col-12"></div>
                                    </div>
                                    <br>
                                </form>
                                <script>
                                    function submit_button() { $('#delete_form').submit(); }
                                </script>
                                <div class="col-2"></div>
                                <div class="col-9">
                                    <a class="btn btn-danger text-white ml-5" onClick="submit_button();">設備削除</a>
                                    {{ BackButton::form() }}
                                </div>
                            @endif

                        </div>

                    </div>                        
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
