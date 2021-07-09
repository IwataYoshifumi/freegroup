@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

#dump( Request::all() );
#dump( session( 'back_button' ) );

$route_name = Route::currentRouteName();

$auth = auth( 'user' )->user();

$tasks = $returns['tasks'];
$taskprops = $returns['taskprops'];
$tasklists = $returns['tasklists'];

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.task.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.task.find_form' )
                    
                    <!-- 一覧フォーム -->
                    @php
                        $columns_name = [ '', '期日', '作成者', 'タスク名','タスクリスト名' ];
                    @endphp
                    
                    <div class="m-1 p-1 border clearfix">
                        <div class="row">
                            @foreach( $columns_name as $name ) 
                                <div class="col">{{ $name }}</div>
                            @endforeach
                        </div>
                        @foreach( $tasks as $task )
                            @php
                                $route_to_show =  route( 'groupware.task.show', [ 'task' => $task->id ] );
                                $disabled = ( $auth->can( 'view', $task )) ? "" : "disabled";
                                $tasklist = $task->tasklist;
                                $taskprop = $taskprops[$task->tasklist_id];
                            
                                $row_class = ( $task->status == "完了" ) ? "bg-light" : ""; 
                                
                            @endphp
                            
                        
                            <div class="row {{ $row_class }}">
                                <div class="col">
                                    <a class="btn btn-sm btn-outline-secondary {{ $disabled }}" href="{{ $route_to_show }}">詳細</a>
                                </div>
                                <div class="col">{{ $task->p_due() }}
                                    @if( $task->status == "完了" )
                                        <span title="完了（日時：{{ $task->completed_time }}　完了者：{{ op( op($task->complete_user)->dept)->name }}  {{ op( $task->complete_user )->name }}）" class="uitooltip">@icon( check-circle )</span>
                                    @endif
                                
                                </div>
                                <div class="col">{{ op( $task->user )->name }}</div>
                                <div class="col">{{ $task->name             }}</div>
                                <div class="col" style='{{ $taskprop->style() }}'>{{ $taskprop->name }}
                                    @if( $tasklist->name != $taskprop->name )
                                        <span style="color: gray" title='管理者設定名：{{ $tasklist->name }}'>@icon( info-circle )</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @if( method_exists( $tasks, 'links' )) 
                            <div class="col mt-2">
                                {{ $tasks->appends( $request->all() )->links() }}
                                
                            </div>
                        @endif
                    </div>

                    <div class="w-100"></div>
                    @if( count( $tasks )) 
                        {{ OutputCSV::button( [ 'route_name' => 'groupware.task.csv', 'inputs' => $request->all() , 'method' => 'GET' ]) }}
                    @endif
                   
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

