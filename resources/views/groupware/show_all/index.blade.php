@php
use App\myHttp\Schedule\Models\Schedule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\ScreenSize;

use App\Models\Customer;

if_debug( __FILE__, Request::all() );
#if_debug( session( 'back_button' ) );

$route_name = Route::currentRouteName();

$data      = ( isset( $returns['data']      )) ? $returns['data']      : [];
$schedules = ( isset( $returns['schedules'] )) ? $returns['schedules'] : [];
$tasks     = ( isset( $returns['tasks']     )) ? $returns['tasks']     : [];
$reports   = ( isset( $returns['reports']   )) ? $returns['reports']   : [];

@endphp

@extends('layouts.app')
@section('content')

@if( ! ScreenSize::isMobile() )
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.show_all.index_menu_button' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>
                <div class="card-body">
@else
            @include( 'groupware.show_all.index_menu_button' )
@endif

                    
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @include( 'groupware.show_all.find_form' )

                    <table class="table table-striped table-hover table-sm bg-white" id="list_table">
                        @foreach( $data as $d )
                            @if( $loop->first )
                                <tr class="w-100 text-truncate">
                                    <th style="width: 15%">作成者</th>
                                    <th style="width: 50%">件名</th>
                                    <th style="width: 35%">日時</th>
                                </tr>
                            @endif
                        
                            @php
                            $task_complete_style = "";
                            if( $d->type == "schedule" ) {
                                $obj  = $schedules->find( $d->id );
                                $href = route( 'groupware.schedule.show', [ 'schedule' => $d->id ] );
                                $data = "data-object='schedule' data-object_id=" . $obj->id;
                                
                            } elseif( $d->type == "task" ) {
                                $obj  = $tasks->find( $d->id );
                                $href = route( 'groupware.task.show', [ 'task' => $d->id ] );
                                $data = "data-object='task' data-object_id=" . $obj->id;

                                $task_complete_style = ( $obj->status == "完了" ) ? "text-decoration:line-through; color: gray;" : "";
                                
                            } elseif( $d->type == "report" ) {
                                $obj  = $reports->find( $d->id );
                                $href = route( 'groupware.report.show', [ 'report' => $d->id ] );
                                $data = "data-object='report' data-object_id=" . $obj->id;

                            }
                            $style = $obj->style();

                            @endphp
                            <tr>
                                <td class="text-truncate p-md-1 btn object_to_show_detail" {!! $data !!}>
                                    {{ op( $obj->user )->name }}
                                    @if( count( $obj->users )) 
                                        @php 
                                        $title = "関連社員：" . count( $obj->users ) . "名"; 
                                        @endphp
                                        <span class="uitooltip" title="{{ $title }}">@icon( user-friends )</span>
                                    @endif
                                </td>
                                <td style="" class="text-truncate p-md-1">
                                    <a class='btn object_to_show_detail' {!! $data !!}>
                                        @if( $d->type == "schedule" )
                                            @icon( calendar )
                                        @elseif( $d->type == "task" ) 
                                            @icon( check-circle )
                                        @elseif( $d->type == "report" ) 
                                            @icon( clipboard )
                                        @endif
                                        <span style="{{ $task_complete_style }}" class="text-truncate">{{ $obj->name }}</span>
                                    </a>
                                </td>
                                <td class="text-truncate p-md-1 btn object_to_show_detail" {!! $data !!}>{{ $obj->p_time( 'index' ) }}</td>
                            </tr>
                            @if( $loop->last )
                                <tr><th colspan=6 class="bg-white m-1 mt-3 p-1">
                                    @if( method_exists( $data, 'links' ))
                                        {{ $data->appends( $request->all() )->links() }}
                                    @endif
                                </th></tr>
                            @endif
                        @endforeach
                    </table>
                    <div class="w-100"></div>
                    @php
                    @endphp

@if( ! ScreenSize::isMobile() )
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!--
  --
  -- 詳細表示ダイヤログ
  --
  -->
@include( 'groupware.show_all.dialog.show_detail' )

<script>
    @if( ScreenSize::isMobile() )
        $('#list_table').width( {{ ScreenSize::width() }} );
        $('#list_table').css( 'table-layout', 'fixed' );
        // $('tbody').width( {{ ScreenSize::width() }});
        //$('tr').width( {{ ScreenSize::width() }});
        $('#list_table').css( 'font-size', 'small' );
    @endif
    $('.object_to_show_detail').css( 'padding', 0 );

</script>


@endsection

