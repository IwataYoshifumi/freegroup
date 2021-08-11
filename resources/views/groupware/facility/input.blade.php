@extends('layouts.app')

@php
use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\ACL;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;

$user = auth( 'user' )->user();

//　オーナー権限アクセスリスト
//
$access_lists = toArray( AccessList::whereOwner( $user->id )->get(), 'name', 'id' );
$access_lists[''] = '';
asort( $access_lists );


//　設備公開種別の選択肢 
//
$facility_types = array_merge( [''=>''], Facility::getTypes() );

$permissions = Task::getPermissions();

if( ! $user->hasRole( 'CanCreateCompanyWideFacilities' ) ) { unset( $facility_types['company-wide'] ); }
if( ! $user->hasRole( 'CanCreatePublicFacilities'      ) ) { unset( $facility_types['public'] );       }

$route_name = Route::currentRouteName();

@endphp

@section('content')

@include( 'groupware.facility.input_script' )

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.facility.menu' )
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>

                <div class="card-body">
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )
                
                    <form method="POST" action="{{ url()->full() }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">*設備名</label>
                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', optional( $facility )->name ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">*大分類</label>
                            <div class="col-md-6">
                                {{ Form::text( 'category', old( 'category', optional( $facility )->category ), ['class' => 'form-control m-1 col-8', ] ) }}
                            </div>
                          
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">小分類</label>
                            <div class="col-md-6">
                                {{ Form::text( 'sub_category', old( 'sub_category', optional( $facility )->sub_category ), ['class' => 'form-control m-1 col-8', ] ) }}
                            </div> 
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">管理番号</label>
                            <div class="col-md-6">
                                {{ Form::text( 'control_number', old( 'control_number', optional( $facility )->control_number ), ['class' => 'form-control m-1', ] ) }}
                            </div>
                            
                            <label for="name" class="col-md-4 col-form-label text-md-right m-1">保管場所</label>
                            <div class="col-md-6">
                                {{ Form::text( 'location', old( 'location', optional( $facility )->location ), ['class' => 'form-control m-1', ] ) }}
                            </div>

                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">*アクセスリスト</label>
                            <div class="col-md-6">
                                {{ Form::select( 'access_list_id', $access_lists, old( 'access_list_id', $access_list->id ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">*設備公開種別</label>
                            <div class="col-md-6">
                                {{ Form::select( 'type', $facility_types, old( 'type', $facility->type ),  [ 'class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1  d-none d-md-block">色サンプル</label>
                            <div class="col-md-6">
                                <span id="sample1" class="col-12 col-md-3 m-1 p-1">色サンプル</span>
                            </div>

                            <label for="backgroud_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">背景色</label>
                            <div class="col-md-6">
                                {{ Form::color( 'background_color', $facility->background_color, [ 'class' => 'col-md-5 form-control m-1', 'id' => 'color', 'onChange' => 'sample()' ] ) }}
                            </div>
                            
                            <label for="text_color" class="col-md-4 col-form-label text-md-right m-1 d-none d-md-block">文字色</label>
                            <div class="col-md-6">
                                {{ Form::color( 'text_color', $facility->text_color, [ 'class' => 'col-md-5 form-control m-1', 'id' => 'text-color', 'onChange' => 'sample()' ] ) }}
                            </div>
                            
                            <script>
                                function sample() {
                                    backgroud_color = $('#color').val();
                                    text_color = $('#text-color').val();
                                    console.log( backgroud_color, text_color );
                                    console.log( $('#sample1').css( 'background-color'));
                                    $('#sample1').css( 'background-color', backgroud_color );
                                    $('#sample1').css( 'color', text_color );
                                }
                                
                                $(document).ready( function() { colsample(); });
                            </script>
                            
                            
                            
                            @if( $route_name == "groupware.facility.update" )
                                <label for="memo" class="col-md-4 col-form-label text-md-right m-1">設備無効化</label>
                                <div class="col-md-6">
                                    <label for="disabled">無効化する</label>
                                    {{ Form::checkbox( 'disabled', 1, old( 'disabled', $facility->disabled ),  [ 'class' => 'form-control m-1', 'id' => 'disabled' ] ) }}
                                </div>
                                
                                @if( old( 'disabled' )) 
                                    
                                    <label for="memo" class="col-md-4 col-form-label text-md-right m-1">設備無効化の再確認</label>
                                    <div class="col-md-6">
                                        <label for="comfirm-disabled_1">無効化後は、設備予約及び予約の変更キャンセルもできません。</label>
                                        {{ Form::checkbox( 'comfirm_disabled[0]', 1, false,  [ 'class' => 'form-control m-1', 'id' => 'comfirm-disabled_1' ] ) }}

                                    </div>
                                
                                @endif
                                
                                @push( 'javascript' )
                                    <script>
                                        $( function() {
                                            $('#not_use').checkboxradio();
                                            $('#disabled').checkboxradio();
                                            $('#init_users_default_permission').checkboxradio();
                                            $('#comfirm-disabled_1').checkboxradio();
                                            $('#comfirm-disabled_2').checkboxradio();
                                        });
                                    </script>
                                @endpush
                            @endif
                            
                            <label for="memo" class="col-md-4 col-form-label text-md-right m-1">備考</label>
                            <div class="col-md-6">
                                {{ Form::textarea( 'memo', old( 'memo',  optional( $facility )->memo ), ['class' => 'form-control m-1' ] ) }}
                            </div>
                            
                            <label for="mobile" class="col-md-4 col-form-label text-md-right m-1">添付ファイル</label>
                            <div class="col-md-6">
                                <!--- コンポーネント InputFilesComponent --->                                
                                
                                <x-input_files2 :input="$component_input_files" />
                            </div>
                        </div>
                            
                            
                            
                        </div>                        
                            
                        <div class="col-12"></div>
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">登録</button>
                                {{ BackButton::form() }}
                                </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@if( $route_name == "groupware.facility.update" )
    @stack( 'javascript' )
@endif

@endsection
