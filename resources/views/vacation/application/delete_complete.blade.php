@extends('layouts.app')

@php

use Illuminate\Support\Facades\Route;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

use App\Http\Helpers\BackButton;

$user      = $application->user;
$approvals = $application->approvals;
//dump( $approvals );
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                <div class='row'>
                    <div class='col-12'>
                        @if (Session::has('flash_message'))
                             <div class="alert alert-warning">{!! Session::get('flash_message') !!}</div>
                        @endif

                        <div class="alert alert-danger m-2">
                            間違いなければ、取り下げ確認をチェックして、取り下げ実行ボタンを押してください。
                        </div>
                    </div>
                </div>


                @include( 'vacation.application.parts_show' )


                        <div class="container">
                            <div class="row w-100 m-2">

                                @auth( 'admin' )
                                    @if( $application->status == "休暇取得完了" and Route::currentRouteName() == "vacation.application.delete_complete" )
                                        {{ Form::open( [ 'url'    => route( 'vacation.application.deleted_complete', [ 'application' => $application->id ] ), 
                                                         'class' => 'w-100', 'name' => 'delete_form' ] ) }}
                                            @csrf
                                            @method( 'POST' )
                                            <div class="checkbox d-flex justify-content-center">
                                                <input type="checkbox" name="delete_comfirm" value=1 class="col-1">
                                                <label class="font-weight-bold">
                                                    取り下げ確認
                                                </label>
                                            </div>
                                            <div class="btn btn-danger col-4" onClick="document.delete_form.submit()">取り下げ　実行</div>
                                            {{ BackButton::form( 'col-2' ) }}
                                        {{ Form::close() }}
                                    @endif
                                @endauth
                                @auth( 'user' )
                                    {{ BackButton::form() }}
                                @endauth

                            </div>
                        </div>
                    </div>
                    
                </form>
                </div>
                </div>

        </div>
    </div>
</div>
@php

@endphp 

@endsection

