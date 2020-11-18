@extends('layouts.app')
@php
    use App\Http\Helpers\BackButton;
    use App\Http\Helpers\MyForms;
#dump( $user );
@endphp


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">従業員　詳細情報</div>
                
                <div>          
                    @if (Session::has('info_message'))
                        <div class="alert alert-warning h5 flush">{!! Session::get('info_message') !!}</div>
                    @endif

                    @if (Session::has('flash_message'))
                        <div class="alert alert-primary h5 flush">{!! Session::get('flash_message') !!}</div>
                    @endif

                    @if (Session::has('error_message'))
                        <div class="alert alert-warning h5 flush">{!! Session::get('error_message') !!}</div>
                    @endif
                </div>
                <script>
                    $( function() {
                       setInterval( function() {
                       $('.blink-btn').fadeOut(500,function(){ $(this).fadeIn(500) });
                       },1000 );
                    });
                    
                    $( function() {
                        setInterval( function() {
                           $('.flush').fadeOut(300, function(){ $(this).fadeIn(300) }); 
                        }, 2300);
                    });
                    
                </script>

                <div class="card-body col-sm-12">
                    <div class="border border-dark rounded">
                        <h6 class="bg-success text-white w-100 p-2">従業員情報</h6>
                        <div class="row m-1">
                            @if( $user->is_retired() ) 
                                <div class="col-sm-10 m-1 bg-warning align-middle text-nowrap">退職済（退職日：{{ $user->date_of_retired }}）</div>
                                <div class="w-100"></div>
                                
                            @endif
                            <div class="col-md-3 m-1 align-middle font-weight-bold d-none d-md-block">部署名</div>
                            <div class="col-md-7 m-1 align-middle">{{ $user->department->name }}</div>
                            <div class="w-100"></div>
                            <div class="col-md-3 m-1 align-middle font-weight-bold d-none d-md-block">職級</div>
                            <div class="col-md-7 m-1 align-middle">{{ $user->grade }}</div>
                            <div class="w-100"></div>
                            <div class="col-md-3 m-1 align-middle font-weight-bold d-none d-md-block">名前</div>
                            <div class="col-md-7 m-1 align-middlee">{{ $user->name }}</div>
                            <div class="w-100"></div>
                            <div class="col-md-3 m-1 align-middle font-weight-bold d-none d-md-block">メールアドレス</div>
                            <div class="col-md-7 m-1 align-middle text-nowrap">{{ $user->email }}</div>
                            <div class="w-100"></div>
                            @auth( 'admin' )
                                <div class="col-md-3 m-1 align-middle font-weight-bold d-none d-md-block">閲覧権限</div>
                                <div class="col-md-7 m-1 align-middle text-nowrap">{{ $user->browsing }}</div>
                                <div class="w-100"></div>
                                <div class="col-sm-3 m-1 align-middle font-weight-bold d-none d-md-block">備考</div>
                                <div class="col-sm-7 m-1 align-middle">{{ $user->memo }}</div>
                                <div class="col-12">※備考は管理者のみ閲覧可能</div>
                            @endauth
                        </div>
                    </div>
                    </div>
                    
                    <div class="border border-dark rounded mt-3">
                        <h6 class="bg-success text-white w-100 p-2">休暇取得状況</h6>
                        <div class="row m-1">
                            <div class="row w-100 m-1">
                                <div class="col font-weight-bold">年度</div>
                                <div class="col font-weight-bold">有給割当日数</div>
                                <div class="col font-weight-bold">有給消化日数</div>   
                                <div class="col font-weight-bold">有給残日数</div>   
                                <div class="col font-weight-bold">有効期限</div>   
                            </div>
                            @foreach( $user->paidleaves as $paidleave ) 
                                <div class="row w-100 m-1">
                                    <div class="col">
                                    <a class='btn btn-outline-primary btn-sm' 
                                              href='{{ route( 'vacation.paidleave.show', [ 'vacation' => $paidleave ] ) }}'>{{ $paidleave->year }}年度</a>
                                    </div>

                                    <div class="col">{{ $paidleave->print_allocated_num() }}</div>
                                    <div class="col">{{ $paidleave->print_digest_num()  }}</div>   
                                    <div class="col">{{ $paidleave->print_remains_num() }}</div>   
                                    <div class="col">{{ $paidleave->expire_date }}</div>   
                                </div>
                                

                            @endforeach
                        </div>
                    </div>
                    
                    <div class="border border-dark rounded mt-3">
                        <h6 class="bg-success text-white w-100 p-2">直近半年の休暇取得状況</h6>
                        <div class="row m-2 w-95">

                        </div>
                        <table class="table table-border w-90 m-2">
                            <tr class=''>
                                <th class=''>詳細</th>
                                <th class=''>種別</th>
                                <th class=''>申請状況</th>
                                <th class=''>休暇期間</th>
                                <th class=''>休暇日数</th>
                                <th class=''>休暇理由</th>

                            </tr>
                            @foreach( $applications as $application ) 
                                <tr class="">
                                    <td class=""><a class='btn btn-success' href='{{ route( 'vacation.application.show', [ 'application' => $application->id ] ) }}'>詳細</a></td>
                                    <td class="">{{ $application->type   }}</td>
                                    <td class="">{{ $application->status }}</td>
                                    <td class="">{{ $application->print_period() }}</td>
                                    <td class="">{{ $application->print_num() }}</td>
                                    <td class=''>{{ $application->reason }}</td>
                                </tr>
                            @endforeach
                        </table>

                        <div class="row container">
                            <div class="col-12 m-3">
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
#dump( session()->all(), $applications, request() );
@endphp

@endsection
