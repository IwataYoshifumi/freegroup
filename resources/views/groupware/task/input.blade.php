@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Search\GetAccessLists;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\SubClass\GetTaskListForTaskInput;

use App\myHttp\GroupWare\View\groupware_models_customer_input_customers;

// if_debug( $task );

//　初期化
//
$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();
$creator   = ( $task->creator ) ? $task->creator : $auth;
$updator   = ( $task->updator ) ? $task->updator : null;
$customers = old( 'customers', optional( $task )->customers ); 
$users     = old( 'users',     optional( $task )->users ); 
$attached_files = old( 'attached_files', optional( $task->files )->toArray() );

$permissions = Task::getPermissions();


//　タスクリストの変更の変更はカレンダー作成者のみ可能
//
if( $route_name == 'groupware.task.create' ) {
    $tasklists = toArrayWithEmpty( GetTaskListForTaskInput::user( $auth->id ), 'name', 'id' );
    // 変更権限をタスクリストの初期設定に変更するための処理

    $input_tasklist_enable = true;

} elseif( $route_name == 'groupware.task.edit' and $task->user_id == $auth->id ) {
    $tasklist = $task->tasklist;
    $tasklists = toArrayWithEmpty( GetTaskListForTaskInput::getFromUserAndTaskList( $auth->id, $tasklist ), 'name', 'id' );

    $input_tasklist_enable = true;

} else {
    $input_tasklist_enable = false;
}

if( isset( $tasklists )) {
    $TaskLists = TaskList::whereIn( 'id', array_keys( $tasklists ) )->get();
}


#dd( $creator->name, $updator );
#if_debug( $attached_files, old( 'attached_files' ) );

// エラー表示
$is_invalid['name'       ] = ( $errors->has( 'name'                ) ) ? 'is-invalid' : '';
$is_invalid['date_time'  ] = ( $errors->has( 'start_less_than_end' ) ) ? 'is-invalid' : '';
$is_invalid['tasklist_id'] = ( $errors->has( 'tasklist_id'         ) ) ? 'is-invalid' : '';

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.task.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url' => url()->full(),  "enctype" => "multipart/form-data", 'id' => 'input_form' ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $task )->id ) }}
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 mt-1 col-form-label text-md-right">作成者・更新者</label>
                            <div class="col-md-7 m-1">
                                {{ $creator->dept->name }} {{ $creator->name }} {{ $creator->grade }}
                                @if( $creator->id != op( $updator )->id and $updator )
                                    更新者：{{ $updator->dept->name }}{{ $updator->name }}
                                @endif
                                
                            </div>
                        
                            <label for="name" class="col-md-4 mt-1 col-form-label text-md-right">タスク名</label>
                            <div class="col-md-7">
                                <input type="text" name="name" value="{{ old( 'name', optional( $task )->name ) }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror">
                            </div>
                        
                            <label for="place" class="col-md-4 mt-1 col-form-label text-md-right">タスクリスト</label>
                            <div class="col-md-8">
                                @if( $input_tasklist_enable ) 
                                    {{ Form::select( 'tasklist_id', $tasklists, $task->tasklist_id, [ 'class' => 'w-80 form-control', 'id' => 'tasklist' ] ) }}
                                    <script>

                                        //　タスクリストを切り替えたときに、変更権限を自動的に設定する
                                        //
                                        var tasklist = $('#tasklist');
                                        var permission  = $('#permission' );
                                        var permissions = [];
                                        @foreach( $TaskLists as $tasklist )
                                            permissions[{{ $tasklist->id }}] = "{{ $tasklist->default_permission }}";
                                        @endforeach
                                        
                                        tasklist.on( 'change', function() {
                                            var tasklist_id = $(this).val();
                                            var permission = permissions[ tasklist_id ];
                                            console.log( tasklist_id, permission );
                                            $('#default_permission').val( permission );
                                        });
                                    </script>
                                @else 
                                    {{ $task->tasklist->name }}
                                    {{ Form::hidden( 'tasklist_id', $task->tasklist_id ) }}
                                @endif
                            </div>
                        
                            <label for="email" class="col-md-4 mt-1 col-form-label text-md-right">期限</label>
                            <div class="col-md-8 container">
                                <div class="container row">
                                    @php
                                        $checked = ( $task->all_day ) ? 1 : 0;
                                        $time_span = [ 1 => '１分間隔', 5 => '５分間隔', 10 => '１０分間隔', 15 => '１５分間隔', 30 => '３０分間隔', 60 => '１時間間隔' ];
                                        $date_class = 'date_input form-control col-6 mb-1 '.$is_invalid['date_time'];
                                        $time_class = 'time_input form-control col-4 mb-1 '.$is_invalid['date_time'];
                                    @endphp
                                        {{ Form::text( 'due_date', old( 'due_date', $input->due_date ), [ 'class' => $date_class ,'autocomplete' => 'off', 'id' => 'due_date' ] ) }}
                                        {{ Form::text( 'due_time', old( 'due_time', $input->due_time ), [ 'class' => $time_class ,'autocomplete' => 'off', 'id' => 'due_time' ] ) }}
                                        
                                        <div class="controlgroup">
                                            <label for='all_day'>終日</label>
                                            {{ Form::checkbox( 'all_day', 1, $input->all_day, [ 'class' => '', 'id' => 'all_day' ] ) }}
                                            {{ Form::select( 'time_span', $time_span, old( 'time_span', 60 ), [ 'class' => '', 'id' => 'time_span_select', 'onChange' => 'change()' ] ) }}
                                        </div>
                                </div>
                            </div>
                            
                        @push( 'timepicker_script' )
                            <script>
                            
                                function check_all_day() {
                                    var due_time = $('#due_time')
                                    if( $('#all_day').prop("checked") ) {
                                        due_time.prop( 'disabled', true );
                                    } else {
                                        due_time.prop( 'disabled', false );
                                    }
                                }
                                
                                $('#all_day').on( 'click', function() {
                                    check_all_day();
                                });
                    
                                $(document).ready( function() {
                                    $('.date_input').datepicker( {
                                            scrollDefault: 'now',
                                            //showOtherMonths: true,
                                            selectOtherMonths: true,
                                            numberOfMonths: 2,
                                            showButtonPanel: true,
                                    });
                                
                                
                                    
                                    var options = {
                                        timeFormat: 'H:i',
                                        // defaultDate: moment('2015-01-01'),
                                        useCurrent:'day',
                                        step: $('#time_span_select').val()
                                    };
                                
                                   $('.time_input').timepicker( options );
                                
                                    check_all_day();
                                    
                                });
                                
                                //　時刻間隔セレクトフォーム
                                //
                                function time_span() {
                                    var span = $('#time_span_select').val();
                                    console.log( span );
                                    $('.time_input').timepicker( 'option', 'step', span );
                                }
                                
                                $('#time_span_select').selectmenu( {
                                    change: function( event, ui ) {
                                        var span = $(this).val();
                                        console.log( span, event, ui );
                                        console.log( ui.label, event.type );
                                        $('.time_input').timepicker( 'option', 'step', span );
                                    }
                                });
                                
                    
                            </script>
                        @endpush
                    
                        @if( $route_name == 'groupware.task.create' or 
                           ( $route_name == 'groupware.task.edit' and $creator->id == $auth->id ))
                                <label for="place" class="col-md-4 mt-1 col-form-label text-md-right">変更権限</label>
                                <div class="col-md-8">
                                    {{ Form::select( 'permission', $permissions, $task->permission, [ 'class' => 'form-control col-6', 'id' => 'default_permission' ] ) }}
                                </div>
                        @endif
                    
                        
                            <label for="customers" class="col-md-4 mt-1 col-form-label text-md-right">関連顧客</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <!--x-input_customers :customers="$customers"/>-->
                                <x-checkboxes_customers :customers="$customers" button="顧客検索" />
                            </div>
                            
                            <label for="users" class="col-md-4 mt-1 col-form-label text-md-right">関連社員（出席者）</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <!--x-input_users :users="$users"/>-->
                                <x-checkboxes_users :users="$users" button="社員検索" />
                                
                            </div>
                        
                            <label for="mobile" class="col-md-4 mt-1 col-form-label text-md-right">備考</label>
                            <div class="col-md-8 mt-1">
                                {{ Form::textarea( 'memo', op( $task )->memo, [ 'class' => 'form-control' ] ) }}
                            </div>
                        
                            <label for="mobile" class="col-md-4 mt-1 col-form-label text-md-right">添付ファイル</label>
                            <div class="col-md-8 mt-1">
                                <!--- コンポーネント InputFilesComponent --->                                
                                {{--<x-input_files :attached_files="$attached_files" />--}}
                                
                                <x-input_files2 :input="$component_input_files" />
                            </div>

                            <div class="col-12"></div>
                            <div class="col-md-9 offset-md-4 mt-3 mb-3">
                                @php
                                    if( $route_name == 'groupware.task.create' ) {
                                        $btn_class = 'btn-primary text-white';
                                        $btn_title = '新規　タスク作成';
                                    } elseif( $route_name = 'groupware.task.edit' ) {
                                        $btn_class = 'btn-warning';
                                        $btn_title = 'タスク変更';
                                    }
                                @endphp
                                <a class="btn {{ $btn_class }}" onClick="$('#input_form').submit()">{{ $btn_title }}</a>

                                {{ BackButton::form() }}

                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stack( 'timepicker_script' )


@endsection
