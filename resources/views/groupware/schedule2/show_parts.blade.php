@php
use App\myHttp\GroupWare\Models\User;


@endphp

<div class="container">
    
    <div class="row">
        <label for="name" class="col-12 col-md-4 my_label col-form-label text-md-right">件名</label>
        <div class="col-12 col-md-8">
            {{ $schedule->name }}
        </div>
    
        <label for="place" class="col-12 col-md-4 my_label col-form-label text-md-right">場所</label>
        <div class="col-12 col-md-8">
            {{ $schedule->place }}
        </div>
        
        <label for="place" class="col-12 col-md-4 my_label col-form-label text-md-right">作成者</label>
        <div class="col-12 col-md-8">
            {{ $schedule->user->dept->name }} {{ $schedule->user->name }}
        </div>
    
        <label for="place" class="col-12 col-md-4 my_label col-form-label text-md-right">カレンダー</label>
        <div class="col-12 col-md-8">
            {{ op( $schedule->calendar )->name }}

            @if( $calendar->is_disabled() )
                <span class="uitooltip" title="カレンダー管理者による制限によって、無効化されました。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @elseif( $calendar->is_not_use() )
                <span class="uitooltip" title="カレンダー管理者による制限によって、新規で予定追加できません。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </div>
    
        <label for="email" class="col-12 col-md-4 my_label col-form-label text-md-right">日時</label>
        <div class="col-12 col-md-8">
            {{ $schedule->p_dateTime() }}
        </div>
    
        @if( count( $customers ))    
            <label for="customers" class="col-12 col-md-4 my_label col-form-label text-md-right">関連顧客</label>
            <div class="col-12 col-md-8">
                @foreach( $customers as $c )
                    <div class="col-12">
                        <a href="{{ route( 'customer.show', [ 'customer' => $c->id ] ) }}" class="btn btn-sm btn-outline-secondary">詳細</a>
                        {{ $c->name }} {{ $c->p_age() }}
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $users ))
            <label for="customers" class="col-12 col-md-4 my_label col-form-label text-md-right">関連社員</label>
            <div class="col-12 col-md-8">
                @foreach( $users as $u )
                    <div class="col-12">
                        【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $reports )) 
            <label for="customers" class="col-12 col-md-4 my_label col-form-label text-md-right">関連日報</label>
            <div class="col-12 col-md-8">
                @foreach( $reports as $r )
                    @php
                        $route_to_report = route( 'groupware.report.show', [ 'report' => $r->id ] );
                    @endphp
                
                    <div class="col-12 mb-1">
                        <span class="w-20">{{ $r->user->name }}</span>
                        <a class="btn btn-sm w-80 btn-outline-dark uitooltip" title="作成日時：{{ $r->updated_at }}" href="{{ $route_to_report }}">{{ $r->name }}</a>
                    </div>        
                @endforeach
            </div>
        @endif
    
        
        <label for="mobile" class="col-12 col-md-4 my_label col-form-label text-md-right">添付ファイル</label>
        <div class="col-12 col-md-8">
            @foreach( $files as $file ) 
                <div class="col-12">
                    <div class="row">
                        @php
                            $route_file_download = route('groupware.file.download', [ 'file' => $file->id, 'class' => 'schedule', 'model' => $schedule->id ] );
                            $route_file_view     = route('groupware.file.view',     [ 'file' => $file->id, 'class' => 'schedule', 'model' => $schedule->id ] );
                            if( $auth->can( 'view', $file )) {
                                $route_file_show     = route('groupware.file.show',  [ 'file' => $file->id ] ); 
                            } else {
                                $route_file_show = "";
                            }
                        @endphp
                        <a href="{{ $route_file_view     }}" class="btn btn_icon"> @icon( search ) </a>
                        <a href="{{ $route_file_download }}" class="btn btn_icon"> @icon( file-download ) </a>
                        <span class="" title='アップロード者：{{ $file->user->name }} アップロード日時：{{ $file->created_at }}'>{{ $file->file_name }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    
    
        <label for="mobile" class="col-12 col-md-4 my_label col-form-label text-md-right">備考</label>
        <div class="col-12 col-md-8">
            <pre>{{ $schedule->memo }}</pre>
        </div>
        
        @if( optional( $schedule )->google_calendar_event_id )
            <label for="mobile" class="col-12 col-md-4 my_label col-form-label text-md-right">Google Calendar Event ID</label>
            <div class="col-12 col-md-8">
                {{ $schedule->google_calendar_event_id }}
            </div>
        @endif
    
    </div>
</div>