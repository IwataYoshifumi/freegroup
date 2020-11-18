@extends('layouts.app')

@php
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

//　変数の初期化
//
if( empty( $find['status'] )) { $find['status'] = ""; }
if( empty( $find['date'] ))   { $find['date']   = ">="; }
if( empty( $find['date_operator'] )) { $find['date_operator'] = ">="; }

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            @include( 'vacation.approval.menu' )
            <div class="card">
                <div class="card-header bg-primary text-wight-bold text-white font-size-5">承認一覧</div>
                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )
        
                <div class="card-body">

                    <div class="border border-dark rounded">
                        <h6 class="bg-primary text-white w-100 p-2">承認者</h6>
                        <div class="row m-1">
                            <div class="col-sm-3 m-1 bg-light align-middle">{{ $user->department->name }}</div>
                            <div class="col-sm-2 m-1 bg-light align-middle">{{ $user->grade }}</div>
                            <div class="col-sm-4 m-1 bg-light align-middle">{{ $user->name }}</div>
                        </div>
                    </div>

                    <div class="border border-dark rounded mt-3">
                        <h6 class="bg-primary text-white w-100 p-2">休暇申請一覧</h6>
                        <div class='m-1'>
                            
                        <div class="m-1 w-95">
                            {{ Form::open( [ 'url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
                            @csrf

                            <div class='row'>
                            <div class='col-4'>
                            <label>申請日</label>
                            {{ Form::date( 'find[date]', $find['date'] ) }}
                            {{ Form::select( 'find[date_operator]', ['>=' => '以降', '<=' => '以前' ], $find['date_operator'] ) }}
                            </div>
                            <!--
                            <div class='col-4'>
                            今後の対応
                    
                            <label>申請者</label>
                            {{ Form::text( 'find[applicant_name]', ( ! empty( $find['applicant_name'] )) ? $find['applicant_name'] : '' ) }}

                            
                            </div>
                            -->

                            <div class='col-4'>
                            <label>ステータス</label>
                            {{ Form::select( 'find[status]', ['' => '', '承認待ち'=>'承認待ち', '承認'=>'承認', '却下' =>'却下' ],
                                                $find['status'] )  }}
                            {{ Form::submit( '検索', [ 'class' => 'm-1' ] ) }}

                            </div>
                            </div>

                            
                            <div class='row'>
                            <div class='col-8'>
                           <label>表示数</label>
                            {{ Form::select( 'find[pagination]',[5 => 5,10=>10, 15=>15], $find['pagination'], 
                                             [ 'class' => 'm-1', 'label' => '表示数' ] ) }}

                            </div>
                            </div>
                        {{ Form::close() }}

                        
                        </div>
                        
                            
                        <div class="">
                            @php 
                                $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                                                '却下'     => 'alert-danger font-weight-bold text-danger',
                                                '取り下げ' => 'alert-warning font-weight-bold',
                                                '休暇取得完了' => 'alert-success font-weight-bold' ];
                            @endphp
                        <table class='table table-border m-1 w-90'>
                            <tr class='table table-border'>
                                <th>詳細</th>
                                <th>申請者</th>
                                <th>休暇種別</th>
                                <th>申請日</th>
                                <th>休暇期間</th>
                                <th>休暇日数</th>
                                <th>ステータス</th>
                                <th>理由</th>
                            </tr>
                            @php 
                                $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                                                '承認'     => 'alert-success font-weight-bold',
                                                '却下'     => 'alert-danger font-weight-bold text-danger',
                                                '取り下げ' => 'alert-warning font-weight-bold',
                                                '取り下げ（却下）' => 'alert-warning font-weight-bold',
                                                '休暇取得完了' => 'alert-success font-weight-bold' ];
                                #dd( $approvals );
                            @endphp
                            @foreach( $approvals as $app )
                                @php
                                    $application = $app->application;
                                    $applicant   = $app->approver;
                                    #dd( $application, $applicant );
                                @endphp
                                <tr>
                                    <td><a class='btn btn-outline-primary' 
                                           href='{{ route( 'vacation.approval.show', ['approval' => $app] ) }}'>詳細</a></td>
                                    <td><div>{{ $application->user->name }}</div></td>
                                    <td>{{ $application->type }}</td>
                                    <td>{{ $application->date }}</td>
                                    <td>{{ $application->print_period_for_index() }}</td>
                                    <td>{{ $application->print_num() }}</td>
                                    <td><div class='{{ $statusClass[$application->status] }}'>申請：{{ $application->status }}</div>
                                        <div class='{{ $statusClass[$app->status] }}'>承認：{{ $app->status }}</div></td>
                                    <td>{{ $application->reason     }}</td>

                                </tr>
                            @endforeach
                        </table>
                        
                        {{ $approvals->appends( array( 'find' => $find ))->links() }}
                        <script>
                            $('[data-toggle="tooltip"]').tooltip();
                        </script>
                        
                        </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@php
#dd( Session::all() );
@endphp 

@endsection

