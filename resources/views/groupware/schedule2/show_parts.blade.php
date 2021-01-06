@php
use App\myHttp\GroupWare\Models\User;

@endphp

<div class="container">
    
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
        <div class="col-md-8">
            {{ $schedule->name }}
        </div>
    </div>
    
    <div class="form-group row">
        <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
        <div class="col-md-8">
            {{ $schedule->place }}
        </div>
    </div>
    
    <div class="form-group row">
        <label for="place" class="col-md-4 col-form-label text-md-right">カレンダー</label>
        <div class="col-md-8">
            {{ op( $schedule->calendar )->name }}
        </div>
    </div>
    
    <div class="form-group row">
        <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
        <div class="col-md-8">
            {{ $schedule->p_dateTime() }}
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
    
    @if( count( $reports )) 
        <div class="form-group row">
            <label for="customers" class="col-md-4 col-form-label text-md-right">関連日報</label>
            <div class="col-md-8">
                @foreach( $reports as $r )
                    <div class="col-12">
                        <a href="{{ route( 'groupware.report.show', [ 'report' => $r->id ] ) }}" class="btn btn-sm btn-outline-secondary">詳細</a>
                        【 作成者：{{ $r->user->name }} 】{{ $r->name }}
                    </div>        
                @endforeach
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
        <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
        <div class="col-md-8">
            <pre>{{ $schedule->memo }}</pre>
        </div>
    </div>
    
    @if( optional( $schedule )->google_calendar_event_id )
    <div class="form-group row">
        <label for="mobile" class="col-md-4 col-form-label text-md-right">Google Calendar Event ID</label>
        <div class="col-md-8">
            {{ $schedule->google_calendar_event_id }}
        </div>
    </div>
    @endif

</div>