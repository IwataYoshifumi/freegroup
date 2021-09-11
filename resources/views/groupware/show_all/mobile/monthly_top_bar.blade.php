<div class="d-flex justify-content-between">
    @php
    
    $route_create_schedule = route( 'groupware.schedule.create' );
    $route_create_task     = route( 'groupware.task.create'     );
    
    $previous_date = $base_date->copy()->subMonth()->format( 'Y-m-d' );
    $next_date     = $base_date->copy()->addMonth()->format( 'Y-m-d' );
    $date_title    = $base_date->format( 'Y年 n月' );
    @endphp

    {{--
      --    
      -- 新規予定作成・タスク作成　ボタン ・　サイドバー表示ボタン
      --    
      --}}
    <a class="btn mb_month_btn "          style="width: 5%" title="サイドバー"       id="side_bar_opener">@icon( angle-double-right )</a>
    <a class="btn mb_month_btn uitooltip" style="width: 5%" title="スケジュール作成" href="{{ $route_create_schedule }}">@icon( schedule     )</a>
    <a class="btn mb_month_btn uitooltip" style="width: 5%" title="タスク作成"       href="{{ $route_create_task     }}">@icon( check-circle )</a>
    {{--
      --    
      -- 月切替ボタン
      --    
      --}}
    <div class="" style="width: 60%">
        <div class="d-flex justify-content-center">
            <div class="btn mb_month_btn month_button" data-date="{{ $previous_date }}">@icon( angle-left )</div>
            <div class="btn mb_month_bt">{{ $date_title }}</div>
            <div class="btn mb_month_btn month_button" data-date="{{ $next_date }}"    >@icon( angle-right )</div>
        </div>
    </div>
    
    <div class="" style="width: 25%"></div>

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
        
        
    </script>
</div>