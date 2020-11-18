
<div class="card-body clearfix">
    <div class="border border-dark rounded d-none d-sm-block">
        <h6 class="bg-success text-white w-100 p-2">申請者</h6>
        <div class="row m-1 w-100">
            <div class="col-5  col-sm-3 m-1 bg-light align-middle">{{ $user->department->name }}</div>
            <div class="col-5  col-sm-3 m-1 bg-light align-middle">{{ $user->grade }}</div>
            <div class="col-11 col-sm-5 m-1 bg-light align-middle">{{ $user->name }}</div>
        </div>
    </div>

    <div class="border border-dark rounded mt-3">
        <h6 class="bg-success text-white w-100 p-2">申請内容（ID : {{ $application->id }}）</h6>
        <div class='p-1'>
            <div class="row">
                @php 
                    $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                                    '承認'     => 'alert-success font-weight-bold',
                                    '却下'     => 'alert-danger font-weight-bold text-danger',
                                    '取り下げ' => 'alert-warning font-weight-bold',
                                    '休暇取得完了' => 'alert-success font-weight-bold' ];
                @endphp
                            
                <div class="col-4 text-md-right font-weight-bold">ステータス</div>
                <div class="col-7 {{ $statusClass[$application->status] }}">{{ $application->status }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">申請日</div>
                <div class="col-7">{{ $application->date }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">承認日</div>
                <div class="col-7">{{ $application->approval_date }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">休暇種別</div>
                <div class="col-7">{{ $application->type }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">休暇期間</div>
                <div class="col-7">{{ $application->print_period() }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">休暇日数</div>
                <div class="col-7">{{ $application->print_num() }}</div>
            </div>
            <div class="row">
                <div class="col-4 text-md-right font-weight-bold">休暇理由</div>
                <div class="col-7">{{ $application->reason }}</div>
            </div>
        </div>
    </div>

    <div class="border border-dark rounded mt-3">
        <h6 class="bg-success text-white p-2">承認　申請先</h6>
        <table class='table table-border m-2'>
            <tr>
                <th>部署名</th>
                <th class="d-none d-sm-block">役職</th>
                <th>名前</th>
                <th>ステータス</th>
                <th class="d-none d-sm-block">コメント</th>
            </tr>
            @foreach( $approvals as $approval ) 
                <tr>
                    <td>{{ $approval->approver->department->name }}</td> 
                    <td class="d-none d-sm-block">
                        {{ $approval->approver->grade            }}</td>
                    <td>{{ $approval->approver->name             }}</td>
                    <td>{{ $approval->status                     }}</td>
                    <td  class="d-none d-sm-block">
                        @if( ! is_null( $approval->comment ))
                            <button type="button"
                                    class='btn btn-outline-secondary'
                                    data-toggle="tooltip" data-placement="top"
                                    title='{{ $approval->comment }}444'>
                                    コメント
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            <script>
                $('[data-toggle="tooltip"]').tooltip();
            </script>
        </table>
    </div>
</div>
@php

@endphp 


