
<div class="form-group row bg-light">
    <label for="name" class="col-md-4 col-form-label text-md-right">作成者</label>
    <div class="col-md-8">
        {{ $report->user->name }}
    </div>
</div>

<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
    <div class="col-md-8">
        {{ $report->name }}
    </div>
</div>

<div class="form-group row">
    <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
    <div class="col-md-8">
        {{ $report->place }}
    </div>
</div>

<div class="form-group row">
    <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
    <div class="col-md-8">
        {{ $report->print_time() }}                            
    </div>
</div>

<div class="form-group row">
    <label for="customers" class="col-md-4 col-form-label text-md-right">関連顧客</label>
    <div class="col-md-8">
        @foreach( $customers as $c )
            <div class="col-12">
                <a href="{{ route( 'customer.show', [ 'customer' => $c->id ] ) }}" class="btn btn-sm btn-outline-secondary">詳細</a>
                {{ $c->name }} {{ $c->p_age() }}
            </div>        
        
        @endforeach

    </div>
</div>

<div class="form-group row">
    <label for="customers" class="col-md-4 col-form-label text-md-right">関連社員</label>
    <div class="col-md-8">
        @foreach( $users as $u )
            <div class="col-12">
                【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}
            </div>        
        @endforeach

    </div>
</div>

@if( count( $schedules )) 
    <div class="form-group row">
        <label for="customers" class="col-md-4 col-form-label text-md-right">関連予定</label>
        <div class="col-md-8">
            <div class="row">
                @foreach( $report->schedules as $s )
                    <div class="col-12">
                        <div class="text-truncate d-block">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route( 'groupware.schedule.show', [ 'schedule' => $s->id ] ) }}">詳細</a>
                            {{ $s->name }} 
                            ( {{ $s->print_time() }} )
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="form-group row">
    <label for="mobile" class="col-md-4 col-form-label text-md-right">添付ファイル</label>
    <div class="col-md-8">
        @foreach( $files as $file ) 
            <div class="col-12">
                <div class="row">
 
                    <div class="col-1">
                        <a href="{{ route('groupware.file.show',     [ 'file' => $file->id ] ) }}" target="_blank"><span class='search icon'></span></a>
                    </div>
                    <div class="col-1">
                        <a href="{{ route('groupware.file.download', [ 'file' => $file->id ] ) }}"><span class='download icon'></span></a>
                    </div>
                    <div class="col">{{ $file->file_name }}</div> 
                </div>

            </div>
        @endforeach
    </div>
</div>



<div class="form-group row">
    <label for="mobile" class="col-md-4 col-form-label text-md-right">報告内容</label>
    <div class="col-md-8">
        @if( optional( $report )->memo )
            <pre class="border border-dark p-1">{{ $report->memo }}</pre>
        @else
            <pre class="p-1"></pre>
        @endif
    </div>
</div>
