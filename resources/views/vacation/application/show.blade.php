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
                             <div class="alert alert-primary">{!! Session::get('flash_message') !!}</div>
                        @endif
                    </div>
                </div>
                @auth( 'admin' )
                
                    @if( $application->status == "休暇取得完了" )

                        <div class="panel-group m-3">
                            <div class="panel panel-default">
                                <div class="panel-heading btn text-secondary">
                                    <div class="panel-title font-weight-bold">
                                        <div data-toggle="collapse" class="" href="#collapse1"><i class="fas fa-caret-square-down "></i>
                                        <span class="">管理者メニュー</span></div>
                                    </div>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse">
                                    <div class="panel-body border">
                                        <div class="m-3">
                                            <a href="{{ route( 'vacation.application.delete_complete', [ 'application' => $application->id ] ) }}" class="btn btn-danger">
                                                取得完了済み休暇の取り下げ処理</a>
                                        </div>
                                    </div>
                                    <div class="panel-footer"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                
                @endauth

                @include( 'vacation.application.parts_show' )

                        <div class='row'>
                            <div class="col">
                                @if( $user->id == Auth::id() )
    
                                    @if( $application->status == "承認" )
                                        <a href="{{ route( 'vacation.application.process', [ 'application' => $application ] ) }}" 
                                            class='col-4 btn btn-primary m-2 w-30'>
                                        休暇取得　完了処理
                                        </a>
                                    @endif
                                    @if( $application->status == "承認待ち" or $application->status == "承認" )
                                        {{-- 取り下げボタン --}}
                                        <a href="{{ route( 'vacation.application.drop', [ 'application' => $application ] ) }}"
                                            class='col-4 btn btn-danger m-2 w-30'>
                                            取り下げ
                                        </a>
                                    @endif
                                @endif
                                <div class="m-1">{{ BackButton::form() }}</div>
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

