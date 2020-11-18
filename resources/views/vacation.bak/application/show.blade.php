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
@php

@endphp 

@endsection

