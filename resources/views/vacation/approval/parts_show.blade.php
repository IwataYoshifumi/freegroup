
<div class="card-body">

    <!--
    <div class="border border-dark rounded mb-2 d-none d-lg-block">
        <h6 class="bg-primary text-white w-100 p-2">承認者( {{ $approver->id }} ）</h6>
        <div class="row m-1">
            <div class="col-sm-3 m-1 bg-light align-middle">{{ $approver->department->name }}</div>
            <div class="col-sm-2 m-1 bg-light align-middle">{{ $approver->grade }}</div>
            <div class="col-sm-4 m-1 bg-light align-middle">{{ $approver->name }}</div>
        </div>
    </div>
    -->

    @php 
        $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                        '却下'     => 'alert-danger font-weight-bold text-danger',
                        '取り下げ' => 'alert-warning font-weight-bold',
                        '休暇取得完了' => 'alert-success font-weight-bold' ];
    @endphp

    <div class="border border-dark rounded mb-2">
        <h6 class="bg-primary text-white w-100 p-2">申請者( {{ $applicant->id }} ）</h6>

        <div class="container">
            <div class="d-none d-lg-block m-lg-2">
                <div class="row text-center font-weight-bold">
                    <div class="col-3">部署</div>
                    <div class="col-3">役職</div>
                    <div class="col-6">名前</div>
                </div>
            </div>
            <div class="row text-lg-center m-lg-2">
                <div class="col-6 col-lg-3">
                    {{ $applicant->department->name }}
                </div>
                <div class="col-6 col-lg-3">
                    {{ $applicant->grade }}
                </div>
                <div class="col-12 col-lg-6">
                    {{ $applicant->name }}
                </div>
            </div>
        </div>
    </div>      

    <div class="border border-dark rounded mb-2">
        <h6 class="bg-primary text-white w-100 p-2">申請内容( {{ $application->id }} ）</h6>

        <div class="container">
            <div class="row">
                <div class="col-4 font-weight-bold">申請ステータス</div>
                <div class="col-6">{{ $application->status }}</div>
                <hr class="border">
                
                <div class="col-4 font-weight-bold">休暇種別</div>
                <div class="col-6">{{ $application->type }}</div>


                <div class="col-4 font-weight-bold">申請日</div>
                <div class="col-6">{{ $application->date }}</div>

                <div class="col-4 font-weight-bold">休暇期間</div>
                <div class="col-6">{{ $application->print_period() }}
                </div>
                <div class="col-4 font-weight-bold">休暇日数</div>
                <div class="col-6">{{ $application->print_num() }}</div>
                
                <div class="col-4 font-weight-bold">理由</div>
                <div class="col-6">{{ $application->reason }}</div>
                

                
                <div class="border border-light border-bold col-11"></div>
                <div class="col-12">
                    <div class="container bg-light border border-secondary m-1">
                        <div class="col-12 col-4 font-weight-bold">承認者</div>
                        @foreach( $approvals as $app )
                            <div class="row">
                                <div class="col-4">{{ $app->approver->department->name }}</div>
                                <div class="col-2 d-none d-lg-block">{{ $app->approver->grade }}</div>
                                <div class="col-4 col-lg-3">{{ $app->approver->name  }}</div>
                                <div class="col-4 col-lg-3">{{ $app->status }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@php

@endphp 
