<div class="d-flex justify-content-between">
    @php
    $previous_date = $base_date->copy()->subMonth()->format( 'Y-m-d' );
    $next_date     = $base_date->copy()->addMonth()->format( 'Y-m-d' );
    $date_title    = $base_date->format( 'Y年 n月' );
    @endphp

    {{--
      --    
      -- 新規予定作成・タスク作成　ボタン ・　サイドバー表示ボタン
      --    
      --}}
    <div class="btn" style="width: 5%" title="サイドバー"   id="side_bar_opener">@icon( angle-double-right )</div>
    &nbsp;
    <div class="btn" style="width: 5%" title="設備予約"     onClick="create_new_reservation();">@icon( book )</div>

    
    {{--
      --    
      -- 月切替ボタン
      --    
      --}}
    <div class="" style="width: 60%">
        <div class="d-flex justify-content-center">
            <div class="btn mb_month_btn month_button" data-date="{{ $previous_date }}">@icon( angle-left )</div>
            <div class="btn mb_month_btn">{{ $date_title }}</div>
            <div class="btn mb_month_btn month_button" data-date="{{ $next_date }}"    >@icon( angle-right )</div>
        </div>
    </div>

    <div class="" style="width: 25%">
    </div>

    <script>
        $('.month_button').on( 'click', function() {
            var date = $(this).data('date');
            $("#base_date").val( date );
            $("#search_form").submit();
        });
        
        // サイドメニューの表示
        //
        $('#side_bar_opener').on( 'click', function() {
            console.log( 'clicked side_bar_opener' );
            $('#side_bar').toggle( 'slide', { percent: 50, }, 100 );
        });
        
        //　新規予約
        //
        
        function create_new_reservation() {
            var url = '{{ route( 'groupware.reservation.create' ) }}';
            console.log( 'create_reservation', url );
            open_dialog_to_create_reservation( '' );
        }
        
    </script>
</div>