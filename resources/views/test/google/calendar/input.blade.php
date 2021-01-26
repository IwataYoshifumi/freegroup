@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

@endphp

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'test.google.calendar.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    @php
                        if( Route::currentRouteName() == 'calendar.create' ) {
                            $url = route( 'calendar.store' );
                            $inputs = [];
                        } else {
                            $url = route( 'calendar.edit', [ 'gid' => $inputs['gid'] ] );
                            #if_debug( $inputs );
                        }
                    @endphp

                    <hr>
                    {{ Form::open( [ 'url' => $url ] ) }}
                        @csrf
                        @method( 'POST' )
                        <div class="container border border-dark">
                            <div class="row m-2">
                                <label class="col-4">開始日時</label>
                                <div class="col-8">
                                    {{ Form::date( 'inputs[start]', old( 'input[start]', optional( $inputs )['start'] ), [ 'class' => 'w-50 form-control' ] )  }}
                                    {{ Form::time( 'inputs[start_time]', old( 'input[start_time]', optional( $inputs )['start_time'] ), [ 'class' => 'w-30 form-control' ] ) }}
                                </div>
                                <label class="col-4">終了日時</label>
                                <div class="col-8">
                                    {{ Form::date( 'inputs[end]', old( 'input[end]', optional( $inputs )['end'] ), [ 'class' => 'w-50 form-control' ] )  }}
                                    {{ Form::time( 'inputs[end_time]', old( 'input[end_time]', optional( $inputs )['end_time'] ), [ 'class' => 'w-30 form-control' ] ) }}
                                </div>
                                <label class="col-4">件名</label>
                                <div class="col-8">
                                    {{ Form::text( 'inputs[summary]', old( 'input[summary]', optional( $inputs )['summary'] ), [ 'class' => 'form-control' ] )  }}
                                </div>
    
                                <label class="col-4">場所</label>
                                <div class="col-8">
                                    {{ Form::text( 'inputs[location]', old( 'input[location]', optional( $inputs )['location'] ), [ 'class' => 'w-80 form-control' ] )  }}
                                </div>
    
                                <label class="col-4">備考</label>
                                <div class="col-8">
                                    {{ Form::textarea( 'inputs[description]', old( 'input[description]', optional( $inputs )['description'] ), [ 'class' => 'w-100 form-control' ] )  }}
                                </div>
    
                                <div class="col-8">
                                    @if( Route::currentRouteName() == 'calendar.create' )
                                        <button type="button" class="btn btn-primary" onClick="this.form.submit();">新規予定作成</button>
                                    @else
                                        <button type="button" class="btn btn-warning" onClick="this.form.submit();">変更</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}

                    <div class="w-100"></div>


                </div>
            </div>
        </div>
    </div>
</div>



@endsection