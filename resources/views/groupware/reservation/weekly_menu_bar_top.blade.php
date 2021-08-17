@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();

@endphp

<div class="top_area border border-dark" style="background-color: palegreen" id="top_area">
    <div class="d-flex juscify-content-center">
        <div class="border-dark flex-fill">
            @if( $current_route == "groupware.reservation.monthly" )
                <a class="col btn btn_icon uitooltip" title="週表示" id="switch_reservation_btn" data-action="{{ route( 'groupware.reservation.weekly' ) }}">週</a>
            @elseif( $current_route == "groupware.reservation.weekly" )
                <a class="col btn btn_icon uitooltip text-left" title="月表示" id="switch_reservation_btn" data-action="{{ route( 'groupware.reservation.monthly' ) }}">月</a>                
            @endif
            <script>                
                $("#switch_reservation_btn").on( 'click', function() {
                    console.log( 'aaa' );
                    var search_form = $('#search_form');
                    search_form.attr( 'action', $(this).data('action') );
                    search_form.submit();
                });
            </script>
        </div>
        <div class="flex-fill">
            <div class="row">
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
                <div class="col-1 btn btn_icon month_button" data-date="{{ $previous_date }}">@icon( angle-left )</div>
                <div class="col-8 btn btn_icon font-weight-bold w-100">{{ $date_title }}</div>
                <div class="col-1 btn btn_icon month_button" data-date="{{ $next_date }}"    >@icon( angle-right )</div>
                <div class="btn btn-sm border border-dark m-1" data-date="{{ $today }}">今週</div>                
                
                <script>
                    $('.month_button').on( 'click', function() {
                        var date = $(this).data('date');
                        $("#base_date").val( date );
                        $("#search_form").submit();
                    });
                </script>
            </div>
        </div>
        <div class="flex-fill">                     </div>

    </div>
</div>