@php
use Illuminate\Support\Facades\Route;
use App\Http\Helpers\ScreenSize;

$current_route         = Route::currentRouteName();

@endphp

<div class="top_area border border-dark" style="background-color: palegreen" id="top_area">
    <div class="d-flex juscify-content-between">
        <div class="border-dark flex-fill">
            <a class="btn btn_icon ml-auto" title="新規　設備予約" onClick="open_dialog_to_create_reservation( $(this) );" data-date="{{ op( $request )->base_date }}">@icon( book )</a>
            @if( ScreenSize::isPC() )
                <a class="btn" style="width: 5%" title="予約状況　一覧" href="{{ route( 'groupware.reservation.index' ) }}?from_menu=1">@icon( list )</a>
            @endif
            
            
        </div>

        <div class="flex-fill content-juscify-center">
            @php
            $previous_date = $base_date->copy()->subDays( 7 )->format( 'Y-m-d' );
            $next_date     = $base_date->copy()->addDays( 7 )->format( 'Y-m-d' );
            # $d             = Arr::first( $returns['dates'] );
            // dump( $d, $d->weekNumberInMonth, $base_date->weekNumberInMonth );
            $date_title    = $base_date->format( 'Y年n月' ) . $base_date->weekNumberInMonth. "週目";
            $today = today()->format('Y-m-d');
            @endphp

            {{--
              --
              --
              --　年月表示、月切替ボタン
              --
              --}}
            <div class="row">
                <div class="col-1 btn btn_icon month_button"     data-date="{{ $previous_date }}">@icon( angle-left )</div>
                <div class="col-4 btn btn_icon font-weight-bold w-100">{{ $date_title }}</div>
                <div class="col-1 btn btn_icon month_button"     data-date="{{ $next_date }}"    >@icon( angle-right )</div>
                <div class="col-1 btn btn-sm border border-dark m-1" data-date="{{ $today }}">今週</div>
                <a   class="col-2 btn btn-sm border border-dark m-1" title="月表示" id="switch_reservation_btn" data-action="{{ route( 'groupware.reservation.monthly' ) }}">月表示</a>                
            </div>

            <script>                
                $("#switch_reservation_btn").on( 'click', function() {
                    console.log( 'aaa' );
                    var search_form = $('#search_form');
                    search_form.attr( 'action', $(this).data('action') );
                    search_form.submit();
                });

                $('.month_button').on( 'click', function() {
                    var date = $(this).data('date');
                    $("#base_date").val( date );
                    $("#search_form").submit();
                });
            </script>
        </div>
        
        <div class="flex-fill"></div>

    </div>
</div>