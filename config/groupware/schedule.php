<?php

//  Schedule モデルの設定
//
return [

    'columns' => [ 'name', 'place', 'start_time', 'end_time', 'period', 'memo', 'notice', ],
    'columns_name'  => [    'id'    => 'id' , 
                            'name'  => '件名', 
                            'place' => '場所',
                            'start_time' => '開始日時', 
                            'end_time'   => '終了日時', 
                            'period'     => '期間', 
                            'memo'       => '備考', 
                            'notice'     => '通知',
                        ],
                        
    'permissions' => [ 'creator' => '作成者', 'attendees' => '作成者・参加者', 'writers' => '作成者・参加者・カレンダー編集者全員' ],
    
    
    //　メタ文字は区切り文字として使用不可
    //
    'google_description_separator' => '－－－ Following was Prepended by FreeGroup －－－'

];

