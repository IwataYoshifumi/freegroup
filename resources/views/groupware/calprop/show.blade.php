@extends('layouts.app')

@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;
use App\Http\Helpers\BackButton;

$user = auth( 'user' )->user();
$calendar = $calprop->calendar;
$permissions = Schedule::getPermissions();

$google_private_key_file = $calprop->google_private_key_file();
$route_show_calendar   = route( 'groupware.calendar.show', [ 'calendar' => $calendar ] );
$route_update_calendar = route( 'groupware.calendar.update', [ 'calendar' => $calendar ] );
$route_update_calprop  = route( 'groupware.calprop.update',  [ 'calprop'  => $calprop  ] );
$route_create_schedule = route( 'groupware.schedule.create', [ 'calendar_id' => $calendar->id ] );

$sync_levels = config( 'groupware.calprop.sync_level' );
$sync_spans  = config( 'groupware.calprop.sync_spans' );

$route_update_calprop  = route( 'groupware.calprop.update',  [ 'calprop'  => $calprop  ] );

$google_sync_level = ( $calprop->google_sync_level ) ? $sync_levels[$calprop->google_sync_level] : null;
$google_sync_span  = ( $calprop->google_sync_span  ) ? $sync_spans[ $calprop->google_sync_span ] : null;
$route_gsync       = route( 'groupware.calprop.gsync',       [ 'calprop' => $calprop ] );
$route_gsync_on    = route( 'groupware.calprop.gsync_on',    [ 'calprop' => $calprop ] );
$route_gsync_check = route( 'groupware.calprop.gsync_check', [ 'calprop' => $calprop ] );

$info = "<i class='fas fa-minus-circle' style='color:lightgray'></i>";

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'groupware.calprop.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                        @if( 0 and $calendar->canWrite( user_id() ))
                            <a class="btn btn-success text-white" href="{{ $route_create_schedule }}">予定　新規作成</a>
                        @endif
                        
                        @can( 'update', $calprop )
                            <a class="btn icon_btn" href="{{ $route_update_calprop }}" title="色設定・同期設定　変更">@icon( edit )</a>
                        @endcan
                        
                        @can( 'view', $calendar )
                            <a class="btn icon_btn" href="{{ $route_show_calendar }}" title="カレンダー管理情報"> @icon( config ) </a>
                        @endcan
                        
                        @if( $calprop->google_sync_on ) 
                            <a class="btn btn_icon" href="{{ $route_gsync }}" title="Googleカレンダー手動同期"> @icon( sync ) </a>
                        @elseif( $calprop->google_sync_check )

                            @if( $calprop->google_sync_bidirectional )
                                <a class="btn btn-primary text-white" href="{{ $route_gsync_on }}">同期開始</a>
                                <a class="btn btn-primary text-white" href="{{ $route_gsync_check }}">同期開始＆インポート</a>
                            @else
                                <a class="btn btn-primary text-white" href="{{ $route_gsync_on }}">Googleカレンダー同期開始</a>
                            @endif

                        @elseif( $calprop->is_filled_GoogleConfig() and $user->can( 'update', $calprop ) ) 
                            <a class="btn btn-warning border-dark" href="{{ $route_gsync_check }}">Googleカレンダー同期チェック</a>
                        @endif
                        

                        @include( 'layouts.error' )
                        @include( 'layouts.flash_message' )

                        <div class="form-group row">
                            <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">カレンダー名(管理者設定）</div>
                            <div class="col-12 col-md-6 my_item  m-1">
                                {{ $calendar->name }}
                            </div>
                            
                            <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">カレンダー表示名・色設定</div>
                            <div class="col-11 col-md-6 my_item m-1" style='{{ $calprop->style() }}'>
                                {{ $calprop->name }}
                            </div>
                        
                            <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">カレンダー公開種別{!! $info !!}</div>   {{-- htmlspecialchars OK --}}
                            <div class="col-12 col-md-6 my_item m-1">
                                {{ Calendar::getTypes()[$calendar->type] }}
                            </div>
                        
                            @if( $calendar->not_use )
                                <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">【管理者設定】</div>
                                <div class="col-12 col-md-6 my_item m-1"><span class="alert-danger p-2 text-dark">新規予定追加不可</span></div>
                            @endif

                            @if( $calendar->disabled )
                                <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">【管理者設定】</div>
                                <div class="col-12 col-md-6 my_item m-1"><span class="alert-danger p-2 text-dark">予定修正不可・Googleカレンダー同期不可</span></div>
                            @endif
                            
                            <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">【個人設定】作成予定変更権限（初期値）</div>
                            <div class="col-12 col-md-6 my_item m-1">{{ $permissions[ $calprop->default_permission ] }}</div>

                            @if( $calprop->not_use and ! $calendar->not_use )
                                <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">【個人設定】</div>
                                <div class="col-12 col-md-6 my_item m-1"><span class="alert-warning p-2 text-dark">新規予定追加しない</span></div>
                            @endif

                            @if( $calprop->hide )
                                <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">【個人設定】</div>
                                <div class="col-12 col-md-6 my_item m-1"><span class="alert-warning p-2 text-dark">予定を表示しない</span></div>
                            @endif

                            <!-- 
                              --
                              -- Google 同期設定
                              --
                              -->
                            @if( $calprop->is_filled_GoogleConfig() )
                                @if( $calprop->google_sync_on )
                                    <div class="col-12 col-md-4 my_label col-form-div text-md-right m-1">Google同期</div>
                                    <div class="col-12 col-md-6 my_item m-1">
                                        <span class="bg-warning font-weight-bold p-1">Googleカレンダー同期中</span>
                                    </div>
                                @endif
    
                                <div class="col-12 col-md-4 m-1"></div>
                                <div class="col-12 col-md-6 m-1">
                                    <a class="btn btn-outline-dark btn-sm col-5 m-1"
                                        data-toggle="collapse"
                                        role="button"
                                        href="#google_sync_config"
                                        aria-expand="true"
                                        aria-controls="google_sync_config">Google同期設定</a>
                                </div>
                                <div class="col-12"></div>
                                
                                <div class='col-12 collapse' id="google_sync_config">
                                    <div class="row no-gutters border border-dark border-md-none">
                                        <div class="d-none d-md-block col-4  m-1 text-right">Googleカレンダー同期</div>
                                        <div class="d-block d-md-none col-12 my_label  ">Googleカレンダー同期</div>

                                        <div class="col-12 col-md-6 my_item m-1">
                                            @if( $calprop->google_sync_on ) Googleカレンダー同期中 
                                            @else                           Googleカレンダー非同期
                                            @endif
                                        </div>
    
                                        @if( $calprop->google_sync_on or ! empty( $calprop->google_id ))       
                                            <div class="d-none d-md-block col-4  m-1 text-right">同期レベル</div>
                                            <div class="d-block d-md-none col-12 my_label">同期レベル</div>
                                            <div class="col-12 col-md-7 my_item m-1">
                                                 {{ $google_sync_level }}
                                            </div>
                
                                            <div class="d-none d-md-block col-4  m-1 text-right">同期期間</div>
                                            <div class="d-block d-md-none col-12 my_label">同期期間</div>
                                            <div class="col-12 col-md-7 my_item m-1">
                                                  {{ $google_sync_span }}
                                            </div>
                
                                            <div class="d-none d-md-block col-4  m-1 text-right">同期方向</div>
                                            <div class="d-block d-md-none col-12 my_label">同期方向</div>
                                            <div class="col-12 col-md-7 my_item m-1">
                                                  @if( $calprop->google_sync_bidirectional ) 両方向（ FreeGroup <=> Googleカレンダー）
                                                  @else 片方向（FreeGroup => Googleカレンダー） 
                                                  @endif
                                            </div>
                                            
                                            <div class="d-none d-md-block col-4  m-1 text-right">同期確認</div>
                                            <div class="d-block d-md-none col-12 my_label">同期確認</div>
                                            <div class="col-12 col-md-7 my_item m-1">
                                                @if( $calprop->google_sync_check ) 
                                                    <span class='alert-primary text-primary font-weight-bold p-1'>同期ＯＫ</span>
                                                @elseif( is_null( $calprop->google_updated_at ))  
                                                    <span class='alert-warning font-weight-bold p-1'>未確認</span> 
                                                @else
                                                    <span class='alert-danger text-danger font-weight-bold p-1'>同期ＮＧ</span> 
                                                @endif
                                            </div>
                                            
                
                                            <div class="d-none d-md-block col-4  m-1 text-right">Google カレンダーID</div>
                                            <div class="d-block d-md-none col-12 my_label">Google カレンダーID</div>
                                            <div class="col-12 col-md-7 my_item m-1 text-truncate" title="{{ $calprop->google_calendar_id }}">
                                                 {{ $calprop->google_calendar_id }}
                                            </div>
    
                                            <div class="d-none d-md-block col-4  m-1 text-right">Google サービスアカウントID</div>
                                            <div class="d-block d-md-none col-12 my_label">Google サービスアカウントID</div>
                                            <div class="col-12 col-md-7 my_item m-1 text-truncate" title="{{ $calprop->google_id }}">
                                                 {{ $calprop->google_id }}
                                            </div>
    
                                            <div class="d-none d-md-block col-4  m-1 text-right">サービスアカウント秘密鍵</div>
                                            <div class="d-block d-md-none col-12 my_label">サービスアカウント秘密鍵</div>
                                            <div class="col-12 col-md-7 my_item m-1 text-truncate">
                                                {{ op( $google_private_key_file )->file_name }} 
                                            </div>
                                            
                                            <div class="d-none d-md-block col-4  m-1 text-right">Googleカレンダー同期日時</div>
                                            <div class="d-block d-md-none col-12 my_label">Googleカレンダー同期日時</div>
                                            <div class="col-12 col-md-7 my_item m-1">
                                                @if( $calprop->google_updated_at )
                                                    {{ $calprop->google_updated_at }} 
                                                @else
                                                    未同期
                                                @endif
                                            </div>
                                            
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="col-12 col-md-4 my_label m-1"></div>
                            <ul  class="col-12 col-md-7 m-1">
                                <ui>{!! $info !!}はカレンダー管理者設定</ui>   {{-- htmlspecialchars OK --}}
                            </ul>
                        </div>
                            
                        {{ BackButton::form() }}

                </div>
            </div>
        </div>
    </div>
</div>

@endsection