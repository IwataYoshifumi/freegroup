@php
use App\myHttp\GroupWare\Models\User;



@endphp

<div class="container">
    
    <div class="col-12">&nbsp;</div>
    
    <div class="form-group row">
        <div for="name" class="col-12 col-sm-4 my_label text-left">件名</div>
        <div class="col-12 col-sm-8 text-left text-truncate">
            {{ $report->name }}
        </div>
    
        <div class="col-12 col-sm-4 my_label text-left">場所</div>
        <div class="col-12 col-sm-8 text-left">
            {{ $report->place }}
        </div>
    
        <div class="col-12 col-sm-4 my_label text-left">日報リスト</div>
        <div class="col-12 col-sm-8 text-left">
            {{ op( $report->report_list )->name }}

            @if( $report_list->is_disabled() )
                <span class="alert-danger text-danger font-weight-bold p-1">【無効化中】</span>
            @elseif( $report_list->is_not_use() )
                <span class="uitooltip" title="カレンダー管理者による制限によって、新規で予定追加できません。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </div>
        
    
        <div for="email" class="col-12 col-sm-4 my_label text-left">日時</div>
        <div class="col-12 col-sm-8 text-left">
            {{ $report->p_time() }}
        </div>
    
        @if( count( $customers ))
            <div for="customers" class="col-12 col-sm-4 my_label text-left">関連顧客</div>
            <div class="col-12 col-sm-8">
                @foreach( $customers as $c )
                    <div class="w-100">
                        <a href="{{ route( 'customer.show', [ 'customer' => $c->id ] ) }}" class="btn uitooltip" title="詳細" target="_top"> @icon( address-book ) </a>
                        <span class="text-truncate">{{ $c->name }} {{ $c->p_age() }}</span>
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $users ))
            <div for="customers" class="col-12 col-sm-4 my_label text-left">関連社員</div>
            <div class="col-12 col-sm-8">
                @foreach( $users as $u )
                    <div class="w-100">
                        <a href="{{ route( 'groupware.user.show', [ 'user' => $u->id ] )}}" class="btn" target="_top"> @icon( user ) </a>
                        <span class="text-left text-truncate">【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}</span>
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $schedules )) 
                <div for="customers" class="col-12 col-sm-4 my_label text-left">関連予定</div>
                <div class="col-12 col-sm-8">
                    @foreach( $schedules as $schedule )
                        <div class="w-100">
                            <a href="{{ route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] ) }}" class="btn btn-sm btn-outilne-secondary">
                                <span class="text-truncate text-left">{{ $schedule->start_date }} : {{ $schedule->name }}</span>
                            </a>
                        </div>
                    @endforeach
                </div>
        @endif
    
        @if( count( $files ))
            <div class="col-12 col-sm-4 my_label text-left">添付ファイル</div>
            <div class="col-12 col-sm-8">
                @foreach( $files as $file ) 
                    <div class="col-12">
                        <div class="row">
                            @php
                                $route_file_download = route('groupware.file.download', [ 'file' => $file->id, 'class' => 'report', 'model' => $report->id ] );
                                $route_file_view     = route('groupware.file.view',     [ 'file' => $file->id, 'class' => 'report', 'model' => $report->id ] );
                                if( $auth->can( 'view', $file )) {
                                    $route_file_show     = route('groupware.file.show',  [ 'file' => $file->id ] ); 
                                } else {
                                    $route_file_show = "";
                                }
                            @endphp
                            <a href="{{ $route_file_view     }}" class="btn"> @icon( search ) </a>
                            <a href="{{ $route_file_download }}" class="btn"> @icon( file-download ) </a>                 
                            <span class="" title='アップロード者：{{ $file->user->name }} アップロード日時：{{ $file->created_at }}'>{{ $file->file_name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    
        <div class="col-12">&nbsp;</div>
        <div class="col-12 col-md-4 my_label text-left">備考</div>
        <div class="col-12 col-md-8">
            <pre>{{ $report->memo }}</pre>
        </div>

</div>