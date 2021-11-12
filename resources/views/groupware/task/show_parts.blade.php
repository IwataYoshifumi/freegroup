@php
use App\myHttp\GroupWare\Models\User;

$route_name = Route::currentRouteName();

@endphp

<div class="container">
    
    <div class="form-group row">
        <label for="name" class="col-12 col-md-4 my_label col-form-label text-md-right">件名</label>
        <div class="col-12 col-md-8">
            {{ $task->name }}
        </div>
    
        <label for="place" class="col-12 col-md-4 my_label col-form-label text-md-right">タスクリスト</label>
        <div class="col-12 col-md-8">
            {{ op( $task->tasklist )->name }}

            @if( $tasklist->is_disabled() )
                <span class="alert-danger text-danger font-weight-bold p-1">【無効化中】</span>
            @elseif( $tasklist->is_not_use() )
                <span class="uitooltip" title="タスクリスト管理者による制限によって、新規で予定追加できません。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </div>
        
        <label for="name" class="col-12 col-md-4 my_label col-form-label text-md-right">作成者</label>
        <div class="col-12 col-md-8">
            {{ $task->creator->dept->name }} {{ $task->creator->name }}
        </div>
        
        @php
            $status_class = ( $task->status == "完了" ) ? "task_finished" : "task_unfinish";
        @endphp
        <label for="email" class="col-12 col-md-4 my_label  col-form-label text-md-right">ステータス</label>
        <div class="col-12 col-md-8">
            <span class="col m-1 {{ $status_class }}" id="task_status">{{ $task->status }}</span>

            @can( 'update', $task )
                
                @if( $route_name == "groupware.task.show" or $route_name == "groupware.task.show_modal" )
                    @php
                        $text = ( $task->status == "未完" ) ? "タスクを完了する" : "タスクを未完に戻す";
                    @endphp
        
                    <span class="btn btn-outline btn-outline-dark" data-status="{{ $task->status }}" id="complete_btn">{{ $text }}</span>
                    <script>
                        var complete_btn = $('#complete_btn');
                        complete_btn.on( 'click', function() {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            
                            $.ajax( {
                                url : "{{ route( 'groupware.task.complete', [ 'task' => $task->id ] ) }}",
                                method: "POST",
                                type: "POST",
                                data: { "csrf-token": "{{ csrf_token() }}" }
                            
                            }).done( function( data, status, xhr ) {
                                var status = data['status'];
                                var task_status = $('#task_status');
                                
                                if( status == "完了" ) {
                                    complete_btn.html( 'タスクを未完に戻す' );
                                    task_status.html( '完了' );
                                    task_status.removeClass( 'task_unfinish' );
                                    task_status.addClass( 'task_finished' );
                                } else {
                                    complete_btn.html( 'タスクを完了する' );
                                    task_status.html( '未完' );
                                    task_status.removeClass( 'task_finish' );
                                    task_status.addClass( 'task_unfinish' );
                                }
                            });
                        });
                    </script>
                @endif
            @endcan
            
            
        </div>
    
        <label for="email" class="col-12 col-md-4 my_label  col-form-label text-md-right">期日</label>
        <div class="col-12 col-md-8">
            {{ $task->p_due() }}
        </div>

        @if( $task->status == "完了" )        
            <label for="email" class="col-12 col-md-4 my_label  col-form-label text-md-right">完了日時</label>
            <div class="col-12 col-md-8">
                {{ op( $task->completed_time )->format( 'Y-m-d H:i' ) }} 　完了者：{{ op( op($task->complete_user)->dept)->name }} {{ op( $task->complete_user )->name }}
            </div>
        @endif

        
    
        @if( count( $customers ))
            <label for="customers" class="col-12 col-md-4 my_label  col-form-label text-md-right">関連顧客</label>
            <div class="col-12 col-md-8">
                @foreach( $customers as $c )
                    <div class="col-12">
                        <a href="{{ route( 'customer.show', [ 'customer' => $c->id ] ) }}" class="btn btn_icon uitooltip" title="詳細"> @icon( address-book ) </a>
                        {{ $c->name }} {{ $c->p_age() }}
                    </div>        
                @endforeach
            </div>
        @endif
    
        @if( count( $users ))
            <label for="customers" class="col-12 col-md-4 my_label  col-form-label text-md-right">関連社員</label>
            <div class="col-12 col-md-8">
                @foreach( $users as $u )
                    <div class="col-12">
                        <a href="{{ route( 'groupware.user.show', [ 'user' => $u->id ] )}}" class="btn btn_icon"> @icon( user ) </a>
                        【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}
                    </div>        
                @endforeach
            </div>
        @endif

        {{--    
        @if( count( $schedules )) 
                <label for="customers" class="col-12 col-md-4 my_label  col-form-label text-md-right">関連予定</label>
                <div class="col-12 col-md-8">
                    @foreach( $schedules as $schedule )
                        <div class="col-12">
                            <a href="{{ route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] ) }}" class="btn btn-sm btn-outline-secondary">
                                {{ $schedule->start_date }} : {{ $schedule->name }}
                            </a>
                        </div>
                    @endforeach
                </div>
        @endif
        --}}
    
        @if( count( $files ))
            <label for="mobile" class="col-12 col-md-4 my_label  col-form-label text-md-right">添付ファイル</label>
            <div class="col-12 col-md-8">
                @foreach( $files as $file ) 
                    <div class="col-12">
                        <div class="row">
                            @php
                                $route_file_download = route('groupware.file.download', [ 'file' => $file->id, 'class' => 'task', 'model' => $task->id ] );
                                $route_file_view     = route('groupware.file.view',     [ 'file' => $file->id, 'class' => 'task', 'model' => $task->id ] );
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
    
        <label for="mobile" class="col-12 col-md-4 my_label  col-form-label text-md-right">備考</label>
        <div class="col-12 col-md-8">
            <pre>{{ $task->memo }}</pre>
        </div>

</div>