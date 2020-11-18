@php

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\View\groupware_models_customer_input_customers;

//　初期化
//
#dump( $report, $report->schedules );

$customers = old( 'customers', optional( $report )->customers ); 
$users     = old( 'users',     optional( $report )->users ); 
$user      = User::find( $report->user_id );
$attached_files = old( 'attached_files', optional( $report->files )->toArray() );
$schedules = $report->schedules;

#dump( $schedules );
#dd( $report->user_id, $user );
#dump( $attached_files, old( 'attached_files' ) );

@endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">

            @include( 'groupware.report.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    {{ Form::open( [ 'url'     => route( Route::currentRouteName(), [ 'report' => optional($report)->id ] ), 
                                      "enctype"=>"multipart/form-data", 
                                    ]) }}
                        @method( 'POST' )
                        @csrf
                        {{ Form::hidden( 'id', optional( $report )->id ) }}
                        <div class="form-group row bg-light">
                            <label for="name" class="col-md-4 col-form-label text-md-right">作成者</label>
                            <div class="col-md-8">
                                {{ Form::hidden( 'user_id', optional( $report )->user_id ) }}
                                {{ $user->p_dept_name() }} {{ $user->name }} {{ $user->grade }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">件名</label>
                            <div class="col-md-8">
                                <input type="text" name="name" value="{{ old( 'name', optional( $report )->name ) }}" autofocus class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="place" class="col-md-4 col-form-label text-md-right">場所</label>
                            <div class="col-md-8">
                                <input type="text" name="place" value="{{ old( 'place', optional( $report )->place ) }}" class="form-control  @error('place') is-invalid @enderror">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">日時</label>
                            <div class="col-md-8">
                                <input id="start_time" type="datetime-local" name="start_time" value="{{ old('start_time', optional( $report )->o_start_time() ) }}" class="form-control w-75 @error('start_time') is-invalid @enderror">
                                <input id="end_time"   type="datetime-local" name="end_time"   value="{{ old('end_time',   optional( $report )->o_end_time()   ) }}"   class="form-control w-75 @error('end_time') is-invalid @enderror">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="customers" class="col-md-4 col-form-label text-md-right">関連顧客</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <x-input_customers :customers="$customers"/>
                            </div>
                        </div>

<!--                        
                        @if( $report->schedule_id )
                            <div class="form-group row">
                                <label for="users" class="col-md-4 col-form-label text-md-right">関連予定<label>
                                <div class="col-md-8">
                                    {{ Form::hidden( 'schedule_id', old( 'schedule_id', $report->schedule_id )) }} {{ $report->schedule_id }}
                                </div>
                            </div> 
                        @endif
-->                    
                            
                        <div class="form-group row">
                            <label for="users" class="col-md-4 col-form-label text-md-right">関連社員</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputCustomersComponent --->                                
                                <x-input_users :users="$users"/>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="users" class="col-md-4 col-form-label text-md-right">関連予定</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputScheduleComponent --->       
                                <x-input_schedules :schedules="$schedules"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">報告内容</label>
                            <div class="col-md-8">
                                <div data-toggle="modal" data-target="#memo_form" class="schedule">
                                    <div class="bg-warning">ここをクリックして入力</div>
                                    @if( old( 'memo', optional( $report )->memo )) 
                                        <pre id="show_memo" class="p-1 border border-secondary">{{ old( 'memo', optional( $report )->memo ) }}</pre>
                                    @else
                                        <pre id="show_memo" class="p-1"></pre>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="memo_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">報告内容</h4>
                                    </div>
                                    <div class="modal-body">
                                        <textarea id="form_memo" name="memo" rows=30 cols=100
                                                    class="form-control @error('memo') is-invalid @enderror">{{ old( 'memo', $report->memo ) }}</textarea>
                                        <script>
                                            $('#form_memo').change( function() {
                                                var memo = $(this).val(); 
                                                console.log( memo );
                                                $('#show_memo').html( memo );
                                                $('#show_memo').prop( 'class', 'p-1 border border-secondary');
                                            });
                                            
                                        </script>
            
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">添付ファイル</label>
                            <div class="col-md-8">
                                <!--- コンポーネント InputFilesComponent --->                                
                                <x-input_files :attached_files="$attached_files" />
                            </div>
                        </div>


                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            @if( preg_match( '/report.create$/', Route::currentRouteName() ))
                                <button type="submit" class="btn btn-primary">新規作成</button>
                            @elseif( preg_match( '/report.edit$/', Route::currentRouteName() ))
                                <button type="submit" class="btn btn-warning">　変更実行　</button>
                            @endif
                            {{ BackButton::form() }}

                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
