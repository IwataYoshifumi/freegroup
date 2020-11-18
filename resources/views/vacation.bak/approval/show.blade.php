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
    $approvals     = $application->approvals;
    $applicant     = $application->user;
    $current_route = Route::current()->getName();
    
    $show_btn = TRUE;
@endphp

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @include( 'vacation.approval.menu' )
            </div>

                <div class="col-lg-10 border">
                    <div class="card-header bg-primary text-white font-wight-bold">{{ config( Route::currentRouteName()) }}</div>
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

                    @include( 'vacation.approval.parts_show', 
                            [ 'approvals'   => $approvals, 
                              'approver'    => $approver, 
                              'application' => $application, 
                              'applicant'   => $applicant ] )       
                
                    <div class="container">
                        <div class='row'>
                        
                            @if( $approver->id == Auth::user()->id and 
                               ( $approval->status == "承認待ち" and $application->status == "承認待ち" ))

                                <a class='btn btn-success submit-btn col-3 m-3' href='{{ route('vacation.approval.approve', ['approval' => $approval] ) }}'>承認</a>
                                <a class='btn btn-danger  submit-btn col-3 m-3' href='{{ route('vacation.approval.reject',  ['approval' => $approval] ) }}'>却下</a>

                            @endif
                                        
                            {{ BackButton::form( 'col-2 m-3' ) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        #dd( $approver->id , Auth::user()->id, $approval, $application );


    @endphp 
@endsection


