
@php
use App\Http\Helpers\BackButton;
use App\Models\User;
use App\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

if( ! isset( $customer ) ) { $customer = null; }

$route_name = Route::currentRouteName();

$auth      = auth( 'user' )->user();

$users     = $schedule->users;
$creator   = $schedule->creator;
$updator   = $schedule->updator;
$calendar  = $schedule->calendar;

$files     = $schedule->files;
$customers = $schedule->customers;
$reports   = $schedule->reports;

# dd( $user, $files );

@endphp

@include('layouts.header')

<div class="container">
    <div class="row">
        @if( $schedule->calendar->is_disabled() )
            <div class="alert-warning w-80 m-1 p-2">この予定はカレンダー管理者によって無効化されました。編集・新規追加はできません</div>
        @endif
        
        @include( 'groupware.schedule2.show_button' )
    
        
        @include( 'groupware.schedule2.show_parts' ) 
    </div>
</div>

