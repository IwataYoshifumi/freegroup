
@php
use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Report;

if( ! isset( $customer ) ) { $customer = null; }

$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

$users     = $report->users;
$creator   = $report->creator;
$updator   = $report->updator;
$report_list  = $report->report_list;

$files     = $report->files;
$customers = $report->customers;
$reports   = $report->reports;
$schedules = $report->schedules;

# dd( $user, $files );

@endphp

@include('layouts.header')

<div class="container">
    <div class="row">
        @if( $report->report_list->is_disabled() )
            <div class="alert-warning w-80 m-1 p-2">この日報リストは管理者によって無効化されました。編集・新規追加はできません</div>
        @endif
        
        @include( 'groupware.report.show_button' )
        
        @include( 'groupware.report.show_parts' ) 
    </div>
</div>

