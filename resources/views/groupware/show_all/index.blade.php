@php
use App\myHttp\Schedule\Models\Schedule;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

if_debug( __FILE__, Request::all() );
#if_debug( session( 'back_button' ) );

$route_name = Route::currentRouteName();

$data      = ( isset( $returns['data']      )) ? $returns['data']      : [];
$schedules = ( isset( $returns['schedules'] )) ? $returns['schedules'] : [];
$tasks     = ( isset( $returns['tasks']     )) ? $returns['tasks']     : [];
$reports   = ( isset( $returns['reports']   )) ? $returns['reports']   : [];

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.show_all.index_menu_button' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    @include( 'groupware.show_all.find_form' )

                    <table class="table table-striped table-hover table-sm">
                        @foreach( $data as $d )
                            @if( $loop->first )
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>作成者（関連社員）</th>
                                    <th>件名</th>
                                    <th>日時</th>
                                </tr>
                            @endif
                        
                            @php
                            if( $d->type == "schedule" ) {
                                $obj  = $schedules->find( $d->id );
                                $href = route( 'groupware.schedule.show', [ 'schedule' => $d->id ] );
                                $task_complete_style = "";
                            } elseif( $d->type == "task" ) {
                                $obj  = $tasks->find( $d->id );
                                $href = route( 'groupware.task.show', [ 'task' => $d->id ] );
                                $task_complete_style = ( $obj->status == "完了" ) ? "text-decoration:line-through; color: gray;" : "";
                            } elseif( $d->type == "report" ) {
                                $obj  = $reports->find( $d->id );
                                $href = route( 'groupware.report.show', [ 'report' => $d->id ] );
                                $task_complete_style = "";
                            }
                            $style = $obj->style();

                            @endphp
                            <tr>
                                <td>

                                </td>
                                <td>{{ op( $obj->user )->name }}
                                    @if( count( $obj->users )) 
                                        @php 
                                            $title = "関連社員：" . count( $obj->users ) . "名"; 
                                        @endphp
                                        <span class="uitooltip" title="{{ $title }}">@icon( user-friends )</span>
                                    @endif
                                </td>
                                <td style="" class="p-1">
                                    <a class='btn' href="{{ $href }}" style="">
                                        <span class="">
                                            @if( $d->type == "schedule" )
                                                @icon( calendar )
                                            @elseif( $d->type == "task" ) 
                                                @icon( check-circle )
                                            @elseif( $d->type == "report" ) 
                                                @icon( clipboard )
                                            @endif
                                        </span>
                                        <span style="{{ $task_complete_style }}">{{ $obj->name }}</span>
                                    </a>
                                </td>
                                <td>{{ $obj->p_time() }}</td>
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
        
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

