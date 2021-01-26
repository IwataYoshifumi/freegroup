
<!-- 検索フォーム表示ボタン -->
<button id="search_form_dialog_opener" class="btn btn-sm btn-outline-dark m-1">予定検索</button>

@once
    <!-- スケジュール検索モーダル -->
    <div id="search_form_dialog" title="スケジュール検索">
        
        @include( 'groupware.schedule2.find_form' )
        
        </div>
        <script>
            $( function() {
               $('#search_form_dialog').dialog( { 
                    autoOpen: false,
                    miniHeight: 580,
                    minWidth: 1080,
                    closeOnEscape: true,
                    buttons: [ { text: '検索',   click: function() { $('#search_form').submit(); },
                             //  text: '閉じる', click: function() { $(this).dialog( 'close' );  },
                        }]
                });
            });
        
            $('#search_form_dialog_opener').on( 'click', function() {
               $('#search_form_dialog').dialog( 'open' );
            });
    </script> 
@endonce