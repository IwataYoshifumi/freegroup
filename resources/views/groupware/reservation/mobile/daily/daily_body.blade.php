@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\ScreenSize;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\User;

$user_id = user_id();

$sidebar_height = 30;

$facilities = $returns['facilities'];

setlocale(LC_ALL, 'ja_JP.UTF-8');
#dd( $request->all() );

#if_debug( $request->all() );

$j = 0;
@endphp

<div class="row no-gutters w-100" style="">
    @foreach( $facilities as $facility )

        @php
        $style = $facility->style();
        $reservations = Arr::collapse( [ $returns[$facility->id]['reservations']] );
        @endphp
        @if( $j ) <div class="col-12">&nbsp;</div> @endif
        <div class="col-12 h5 shadow p-1" style="{{ $style }}">{{ $facility->name }}</div>
    
        @if( count( $reservations ))    
            @foreach( $reservations as $reservation )
                @php
                $data = "data-object='reservation' data-object_id=" . $reservation->id;
                @endphp
    
                <div class="reservation_to_show_detail event_item btn col-12 d-flex justify-content-start" {!! $data !!}>
                    <div class="w-20 text-truncate text-left ml-2">
                        {{ $reservation->user->name }}
                    </div> 
                    <div class="w-50 text-truncate text-left ml-2">
                        {{ $reservation->purpose }}
                    </div>
                    <div class="text-truncate text-left ml-2">
                        {{ $reservation->p_time_for_daily_form() }}
                    </div>
                </div>
    
                @php
                $j++;
                @endphp
            @endforeach {{-- loop reservations --}}
        @else
            <div class="event_item btn col-12 d-flex justify-content-start">
                <div class="w-100 text-truncate text-left ml-2">
                    予約なし
                </div> 
            </div>
        @endif
    @endforeach  {{-- loop facilities --}}
</div>

<script>
    // 
    // 詳細表示ダイヤログ（親スクリプトを実行）
    // 
    $('.reservation_to_show_detail').on( 'click', function() {
         window.parent.click_object_to_show_detail( $(this) );
    });

    //
    // モバイル用に表示調整
    //
    @if( ScreenSize::isMobile() ) 
    $('.reservation_to_show_detail').css( 'font-size', 'x-small' );
    $('.reservation_to_show_detail').css( 'padding', '0' );
    @endif

</script>
