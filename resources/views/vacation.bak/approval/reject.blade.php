@extends('layouts.app')

@php

    // Viewの引数
    // url 
    // Approvalモデル　approval 
    //

    use App\Models\Vacation\Application;
    use App\Models\Vacation\User;
    use App\Models\Vacation\Dept;

    use App\Http\Helpers\BackButton;

    #$approver      = Auth::user();
    $approver      = $approval->approver;
    $application   = $approval->application;
    $applicant     = $application->user;
    $approvals     = $application->approvals;
    $current_route = Route::current()->getName();
    
    $show_btn = TRUE;

@endphp



@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            
            @include( 'vacation.approval.menu' )
            
            <div class="card">
                <div class="card-header bg-primary text-white font-wight-bold">{{ config( Route::currentRouteName()) }}</div>

                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )

                @include( 'vacation.approval.parts_show', 
                        [ 'approvals'   => $approvals, 
                          'approver'    => $approver, 
                          'application' => $application, 
                          'applicant'   => $applicant ] )
 
                <div class="container p-3">
                    <div class="row">
                        @if( $approver->id == Auth::user()->id ) 
                            {{ Form::open( [ 'url'    => route( 'vacation.approval.rejected', [ 'approval' => $approval ] ), 
                                            'method' => 'post', 'id' => 'main_form' ] ) }}
                            {{ Form::close() }}
                            <div class="col-4 col-lg-3">
                                <button class='btn btn-danger col-12' id="submit-btn">却下実行</button>
                            </div>
                            <script>
                                $('#submit-btn').click( function() { $('#main_form').submit();  });
                            </script>
                        @endif
                    {{ BackButton::form() }}
                    </div>
                </div>      
            </div>
        </div>
    </div>
</div>


@php

@endphp 

@endsection

