@php 
    use App\Models\Vacation\Vacation;

    $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                    '却下'     => 'alert-danger font-weight-bold text-danger',
                    '取り下げ' => 'alert-warning font-weight-bold',
                    '休暇取得完了' => 'alert-success font-weight-bold' ];
    
    $type = [ '有給休暇' => '有給', '特別休暇' => '特別' ];
@endphp
                            
<div class="container w-100 m-lg-2">

    <div class="container bg-secondary text-white p-lg-2 mt-lg-2 d-none d-lg-block">
        <div class="row mt-1 mrl-1">
            <div class="col-2 ">承認</div>
            <div class="col-2 ">申請者</div>
            <div class="col-1 ">種別</div>
            <div class="col-2 ">申請日</div>
            <div class="col-2 ">休暇期間</div>
            <div class="col-1 ">日数</div>
            <div class="col-2 ">理由</div>
        </div>
    </div>

    @foreach( $approvals as $app )
        <div class="container clearfix mt-1 bg-light border border-dark">
            <div class="row mt-2 p-lg-1">
                <div class=" col-12 col-lg-2">
                    <a class='btn btn-success w-30 w-lg-100' 
                       href='{{ route( 'vacation.approval.show', ['approval' => $app->id ] ) }}'>承認/却下</a>
                </div>
                <div class="col-12 d-lg-none"></div>
                                    
                <div class="col-4 d-lg-none">申請者</div>
                <div class="col-6 col-lg-2">{{ $app->aluser_name }}</div>
                                    
                <div class="col-4 d-lg-none">休暇種別</div>
                <div class="col-6 col-lg-1">{{ $type[$app->type] }}</div>
                                    
                <div class="col-4 d-lg-none">申請日</div>
                <div class="col-6 col-lg-2">{{ $app->al_date     }}</div>
                                    
                <div class="col-4 d-lg-none">休暇期間</div>
                <div class="col-7 col-lg-2">{{ $app->start_date  }}
                    @if( $app->num > 1 ) 
                        ～{{ $app->end_date }}
                    @endif
                </div>
                
                <div class="col-4 d-lg-none">日数</div>
                <div class="col-6 col-lg-1">{{ Vacation::pnum( $app->num )       }}</div>
                
                                    
                <div class="col-4 d-lg-none">理由</div>
                <div class="col-6 col-lg-2">{{ $app->reason      }}</div>

                <div class="w-100 border border-light"></div>
            </div>
        </div>
    @endforeach
</div>
<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>
