<div class="col-12">&nbsp;</div>
@if( count( $reports ))             
    <div class="card">
        <div class="card-header">
            <div class="btn " data-toggle="collapse" data-target="#report_list" aria-expand="false" aria-controls="report_list">
                <i class="fas fa-caret-square-down fa-2x color-inherit link"></i> &nbsp; 日報
            </div>
        </div>
        <div class="card-body collapse" id="report_list">
            <div >
                <div class="row">
                    <div class="col-3 d-none d-lg-block">件名</div>
                    <div class="col-4 d-none d-lg-block">日時</div>
                    <div class="col-4 d-none d-lg-block">社員</div>
                </div>
                <hr class="d-none d-lg-block">
                @foreach( $reports as $r ) 
                    <div class="row">
                        <div class="col-3 d-block d-lg-none">件名</div>
                        <div class="col-8 col-lg-3">
                            <a class="d-block text-trancate" href="{{ route( 'groupware.report.show', [ 'report' => $r->id ] ) }}">{{ $r->name }}</a>
                        </div>
                        <div class="col-3 d-block d-lg-none">日時</div>
                        <div class="col-8 col-lg-4 d-block text-truncate">{{ $r->start_time }}</div>
                        
                        <div class="col-3 d-block d-lg-none">社員</div>
                        <div class="col-8 col-lg-4 d-block text-truncate">{{ $r->user->name }} </div>
                        
                    </div>
                    <hr class="d-block d-none-lg">
                
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="col-12">関連日報なし</div>
@endif

