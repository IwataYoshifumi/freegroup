<?php

//  report モデルの設定
//
return [

    'columns' => [ 'name', 'place', 'start_time', 'end_time', 'period' ],
    'columns_name'  => [    'id'    => 'id' , 
                            'name'  => '件名', 
                            'place' => '場所',
                            'start_time' => '開始日時', 
                            'end_time'   => '終了日時', 
                            'memo'       => '報告事項', 
                            'users'     => '関連社員',
                            'customers' => '関連顧客',
                        ],
                        
        'permissions' => [ 'creator' => '作成者', 'attendees' => '作成者・参加者', 'writers' => '作成者・参加者・日報リスト編集者全員' ],

];

