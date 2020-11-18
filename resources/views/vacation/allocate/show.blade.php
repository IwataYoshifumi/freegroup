@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;

use App\Http\Helpers\BackButton;

$applications = $paidleave->applications;
$user = $paidleave->user;
#dd( $user );
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include( 'vacation.allocate.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                @include( 'layouts.error' )
                @include( 'layouts.flash_message' )
                
                <div class="card-body">
                    
                <div class="card mt-3">
                    <div class="card-header bg-success text-white w-100 p-2">従業員 情報</div>
                    <div class="m-2">
                        <div class="row thead-dark m-2">
                            <div class="col m-1 font-weight-bold">部署</div>
                            <div class="col m-1 font-weight-bold">役職</div>
                            <div class="col m-1 font-weight-bold">名前</div>
                            <div class="col m-1 font-weight-bold">入社年月日</div>
                        </div>
                        <div class="row">
                            <div class="col m-1">{{ $user->department->name }}</div>
                            <div class="col m-1">{{ $user->grade            }}</div>
                            <div class="col m-1">{{ $user->name             }}</div>
                            <div class="col m-1">{{ $user->join_date        }}</div>
                        </div>
                    </div>
                </div>
                    
                    <div class="card mt-3">
                        <div class="card-header bg-success text-white w-100 p-2">有給割当　情報</div>

                            <table class="table col-11 m-3">
                            <tr>
                                <th>割当年度</th>
                                <td>{{ $paidleave->year }}</td>
                            </tr>
                            <tr>
                                <th>割当日</th>
                                <td>{{ $paidleave->allocate_date }}</td>
                            </tr>
                            <tr>
                                <th>有効期限</th>
                                <td>{{ $paidleave->expire_date }}</td>
                            </tr>
                            <tr>
                                <th>有給割当日数</th>
                                <td>{{ $paidleave->print_allocated_num() }}</td>
                            </tr>
                            <tr>
                                <th>申請日数</th>
                                <td>{{ $paidleave->print_application_num() }}</td>
                            </tr>
                            <tr>
                                <th>承認日数</th>
                                <td>{{ $paidleave->print_approval_num() }}</td>
                            </tr>
                            <tr>
                                <th>取得完了日数</th>
                                <td>{{ $paidleave->print_completed_num() }}</td>
                            </tr>
                            <tr>
                                <th>残日数</th>
                                <td>{{ $paidleave->print_remains_num() }}</td>
                            </tr>
                            @if( $paidleave->done_expired )
                                <tr>
                                    <th>期限切れ日数</th>
                                    <td>{{ $paidleave->expired_num }} 日</td>
                                </tr>
                            @endif
                            </table>
                        </div>
                        
                <div class="card mt-3">
                    <div class="card-header bg-success text-white w-100 p-2">関連休暇申請　一覧</div>
                    <div class="m-2">
                        <div class="border bg-light m-2 p-3 row">

                            {{-----------------------------------}}
                            {{--  ステータスチェックボックス　 --}}
                            {{-----------------------------------}}
                            @foreach( config( 'vacation.constant.application.status' ) as $status )
                                @php ( $status == "却下" or $status == "取り下げ" ) ? $checked = '' : $checked = 'checked' @endphp
                                <div class='form-check col-2'>
                                {{ Form::checkbox( "status", $status, $checked, [ 'class' => 'status_check form-check-input' ] ) }}
                                <div class='form-check-label'>{{ $status }}</div>
                                </div>
                            @endforeach
                            <script>
     
                                function click_status_checkbox() {
                                    var checked_status = [  ];
                                    $('.status_check').each( function() {
                                        if( $(this).prop( "checked" ) ) {
                                            checked_status.push( String( $(this).val() ) );
                                        }
                                    });
                                    //console.log( checked_status );
                                    $('.applications').each( function() {
                                        if( $.inArray( String( $(this).data('status') ), checked_status ) >= 0 ) {
                                            $(this).show();
                                        } else {
                                            $(this).hide();
                                        }
                                    });
                                };
                                
                                $('.status_check').click( function() {
                                    click_status_checkbox();
                                });
                                
                                $('.document').ready( function() {
                                    click_status_checkbox();
                                });
                                
                            </script>
                        </div>
                        
                        <table class='table'>
                        <tr>
                            <th>休暇期間</th>
                            <th>休暇日数</th>
                            <th>理由</th>
                            <th>ステータス</th>
                        </tr>

                        @foreach( $applications as $app )
                            @php $class = config( 'constant.application.class.bg_status' ); @endphp
                            <tr class='applications {{ $class[$app->status] }}' data-status='{{ $app->status }}'>
                                <td>{{ $app->print_period() }}</td>
                                <td>{{ $app->print_num() }}</td>
                                <td>{{ $app->reason }}</td>
                                <td>{{ $app->status }}</td>
                            </tr>
                        @endforeach
                        </table>

                    </div>
                    <div class="row">
                        <div class='col m-2'>
                            {{ BackButton::form() }}
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>
@php
   # dd( $paidleave, $user );
    #dd( $request );
@endphp 

@endsection

