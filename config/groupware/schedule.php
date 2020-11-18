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

];

