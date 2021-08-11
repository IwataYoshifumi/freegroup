@php
use App\myHttp\Schedule\Models\Schedule;

@endphp@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );


$route_name = Route::currentRouteName();
$auth = auth( 'user' )->user();

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include( 'groupware.file.menu_button' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    <!-- 検索フォーム -->                    
                    @include( 'groupware.file.index_find' )
                    
                    <!-- 一覧フォーム -->
                    @php
                        $columns_name = [ '', 'ファイル名', '所有者', 'アップロード日時', '添付' ];
    
                    @endphp
                    
                    <div class="m-1 p-1 border clearfix">
                        <div class="d-none d-lg-block">
                            <div class="row container">
                                <div class="col-1">
                                    @if( $route_name == "groupware.file.index"  )
                                        
                                        詳細
                                    @elseif( $route_name == "groupware.file.select" )
                                        削除
                                    @endif
                                    </div>
                                <div class="col-4">ファイル名</div>
                                <div class="col-2">所有者</div>
                                <div class="col-3">アップロード日時</div>
                                <div class="col-1">添付</div>
                            </div>
                        </div>
                        
                        @if( $route_name == "groupware.file.select" ) 
                            {{ Form::open( [ 'route' => 'groupware.file.deleted', 'id' => 'select_delete_form' ] ) }}
                            @method( 'GET' )
                        @endif
                        
                        @foreach( $files as $file )
                            <div class="col-12 border border-light"></div>
                            <div class="row mt-1">
                                @php
                                    $route_show   = route( 'groupware.file.show',   [ 'file' => $file->id ] );
                                    #if( count( $file->schedules ) or count( $file->reports ) or count( $file->calprops )) {
                                    if( count( $file->fileables )) {
                                        $attached = "有";
                                    } else {
                                        $attached = "";
                                    }
                                @endphp
                                
                                <div class="col-12 col-lg-1 d-none d-lg-block">
                                    @if( $route_name == "groupware.file.index" and $auth->can( 'view', $file ) )
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ $route_show }}">詳細</a>
                                    @elseif( $route_name == "groupware.file.select" and $auth->can( 'delete', $file ) )
                                        {{ Form::checkbox( 'files[]', $file->id, "", [ 'class' => 'delete_checkboxes' ] ) }}
                                    @endif
                                </div>

                                <div class="col-12 col-lg-4">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">ファイル名</div>
                                        <div class="col-7 col-lg-12">
                                            @if( is_debug() ) <a title='{{ $file->id }}' href='{{ $route_show }}' class="uitooltip">@icon( debug )</a> @endif
                                            {{ $file->file_name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">所有者</div>
                                        <div class="col-7 col-lg-12">{{ $file->user->name     }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-lg-3">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">アップ</div>
                                        <div class="col-7 col-lg-12">{{ $file->p_created_at() }}</div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-1">
                                    <div class="row">
                                        <div class="col-4 d-block d-lg-none">添付有無</div>
                                        <div class="col-7 col-lg-12">{{ $attached     }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if( $route_name == "groupware.file.select" and count( $files )) 
                        {{ Form::close() }}

                        <div class="m-2">
                            <div class="col-3 btn btn-outline-dark" id="toggle_button" data-value="0">全て選択する</div>
                            
                            <div class="col-3 btn btn-danger" id="submit_button">ファイル削除確認画面へ</div>
                        </div>
                        <script>
                            $('#toggle_button').click( function() {
                                if( $(this).data('value') == 0 ) {
                                    var checked = true;
                                    $(this).data('value', "1" );
                                    $(this).html( '全てチェックを外す' );
                                } else {
                                    var checked = false;
                                    $(this).data('value', "0");
                                    $(this).html('全て選択する');
                                }
                                $('.delete_checkboxes').each( function() {
                                    $(this).prop( 'checked', checked );
                                });
                            });
                            
                            $('#submit_button').click( function() {
                                $('#select_delete_form').submit(); 
                            });
                            
                            
                        </script>

                    @endif

                    <div class="w-100">
                        @if( method_exists( $files, 'links' )) 
                            {{ $files->appends( $request->all() )->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

