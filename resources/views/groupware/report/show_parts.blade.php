@php
use App\myHttp\GroupWare\Models\User;



@endphp

<div class="container">
    
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
        <div class="col-md-8">
            {{ $report->name }}
        </div>
    
        <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
        <div class="col-md-8">
            {{ $report->place }}
        </div>
    
        <label for="place" class="col-md-4 col-form-label text-md-right">日報リスト</label>
        <div class="col-md-8">
            {{ op( $report->report_list )->name }}

            @if( $report_list->is_disabled() )
                <span class="alert-danger text-danger font-weight-bold p-1">【無効化中】</span>
            @elseif( $report_list->is_not_use() )
                <span class="uitooltip" title="カレンダー管理者による制限によって、新規で予定追加できません。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </div>
        
    
        <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
        <div class="col-md-8">
            {{ $report->p_time() }}
        </div>
    
        @if( count( $customers ))
            <label for="customers" class="col-md-4 col-form-label text-md-right">関連顧客</label>
            <div class="col-md-8">
                @foreach( $customers as $c )
                    <div class="col-12">
                        <a href="{{ route( 'customer.show', [ 'customer' => $c->id ] ) }}" class="btn btn_icon uitooltip" title="詳細"> @icon( address-book ) </a>
                        {{ $c->name }} {{ $c->p_age() }}
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $users ))
            <label for="customers" class="col-md-4 col-form-label text-md-right">関連社員</label>
            <div class="col-md-8">
                @foreach( $users as $u )
                    <div class="col-12">
                        <a href="{{ route( 'groupware.user.show', [ 'user' => $u->id ] )}}" class="btn btn_icon"> @icon( user ) </a>
                        【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $schedules )) 
                <label for="customers" class="col-md-4 col-form-label text-md-right">関連予定</label>
                <div class="col-md-8">
                    @foreach( $schedules as $schedule )
                        <div class="col-12">
                            <a href="{{ route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] ) }}" class="btn btn-sm btn-outline-secondary">
                                {{ $schedule->start_date }} : {{ $schedule->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
        @endif
    
        @if( count( $files ))
            <label for="mobile" class="col-md-4 col-form-label text-md-right">添付ファイル</label>
            <div class="col-md-8">
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
    
        <label for="mobile" class="col-md-4 col-form-label text-md-right">備考</label>
        <div class="col-md-8">
            <pre>{{ $report->memo }}</pre>
        </div>

</div>