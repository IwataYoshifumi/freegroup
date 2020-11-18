<div class="row m-2 border border-dark">
    @php
        $route_show     = route( 'groupware.file.show',     [ 'file' => $file->id ] );
        $route_download = route( 'groupware.file.download', [ 'file' => $file->id ] );
    @endphp
    
    <div class="col-12 col-lg-4">ファイル名</div>
    <div class="col-12 col-lg-8">{{ $file->file_name }} &nbsp;
        <a href="{{ $route_show     }}" target="_blank"><i class="fas fa-search"></i></a> &nbsp;
        <a href="{{ $route_download }}"                ><i class="fas fa-download"></i></a> &nbsp;
    </div>

    <div class="col-12 col-lg-4">所有者</div>
    <div class="col-12 col-lg-8">{{ $file->user->dept->name }} {{ $file->user->name }}</div>
    
    <div class="col-12 col-lg-4">アップロード日時</div>
    <div class="col-12 col-lg-8">{{ $file->p_created_at() }}</div>
    
    @if( count( $file->schedules )) 
        <div class="col-12 col-lg-4">添付先（予定）</div>
        <div class="col-12 col-lg-8">
            @foreach( $file->schedules as $s ) 
                @php
                    $href = route( 'groupware.schedule.show', [ 'schedule' => $s->id ] );
                @endphp
                <div class="row">
                    <div class="col-3">{{ $s->name }}</div>
                    <div class="col-3">{{ $s->start_time }}</div>
                    <div class="col-2 mtb-1">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">詳細</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    @if( count( $file->reports )) 
        <div class="col-12 col-lg-4">添付先（日報）</div>
        <div class="col-12 col-lg-8">
            @foreach( $file->reports as $r )
                @php
                    $href = route( 'groupware.report.show', [ 'report' => $r->id ] );
                @endphp
                <div class="row">
                    <div class="col-3">{{ $r->name }}</div>
                    <div class="col-3">{{ $r->start_time }}</div>
                    <div class="col-2 mtb-1">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">詳細</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    @if( count( $file->schedule_types )) 
        <div class="col-12 col-lg-4">添付先（スケジュール種別）</div>
        <div class="col-12 col-lg-8">
            @foreach( $file->schedule_types as $type )
                @php
                    $href = "";
                @endphp
                <div class="row">
                    <div class="col-3">{{ $type->name }}</div>
                    <div class="col-2 mtb-1">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ $href }}">詳細</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    

</div>