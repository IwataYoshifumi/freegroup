@extends('layouts.app')

@php


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;


#if_debug( $access_list, $acls );
#if_debug( $access_list, $lists, $users, $access_list->users );

$reports_num = $report_list->reports()->count();

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

                        <div class="alert-warning col-11">この日報リストには{{ $reports_num }} 件の日報が登録されています。</div>
                        <div class="alert-warning col-11">日報リストを削除することで、これらの日報も全て削除されます。</div>
                        <div class="alert-warning col-11">対応履歴や顧客対応履歴を残すためにも、日報リストを「無効化」するだけで、削除しないことをオススメします。</div>

                        @include( 'groupware.report_list.show_parts' )
                            
                        
                        </div>

                        <div class="form-group row">
                            @if( Route::currentRouteName() == "groupware.report_list.delete" )
                                <form id="delete_form" method="POST" action="{{ url()->current() }}" class="col-12">
                                    @method( 'DELETE' )
                                    @csrf
                                    <div class="row p-1">
                                        <div class="col-2"></div>
                                        <div class="col-9">
                                            <label for="delete_comfirm_1" class="font-weight-bold w-90 ">この日報リストに紐づけされている全ての日報も削除されます</label>
                                            {{ Form::checkbox( 'delete_comfirm[0]', 1, 0, [ 'class' => 'checkboxradio', 'id' => 'delete_comfirm_1' ] ) }} 
                                            
                                            <label for="delete_comfirm_2" class="font-weight-bold w-90 ">削除したデータは復帰できません。この操作は取り消しできません。</label>
                                            {{ Form::checkbox( 'delete_comfirm[1]', 1, 0, [ 'class' => 'checkboxradio', 'id' => 'delete_comfirm_2' ] ) }} 
                                            
                                            <label for="delete_comfirm_3" class="font-weight-bold w-90 ">対応履歴などの為にも無効化するだけで、データを削除しないことを強くオススメしますが、
                                            それでも削除する場合は操作を続行してください。</label>
                                            {{ Form::checkbox( 'delete_comfirm[2]', 1, 0, [ 'class' => 'checkboxradio', 'id' => 'delete_comfirm_3' ] ) }} 
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
                                    <a class="btn btn-danger text-white ml-5" onClick="submit_button();">日報リスト削除</a>
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
