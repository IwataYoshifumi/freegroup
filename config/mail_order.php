<?php

//  メールオーダーの設定
//

return [

    'columns' => [ 'name', 'person', 'email', 'tel', 'fax', 'memo' ],
    'columns_delivery' => [
                    'delivery_date',
                    'delivery_name',
                    'delivery_person',
                    'delivery_tel',
                    'delivery_postcode',
                    'delivery_prefecture',
                    'delivery_city',
                    'delivery_address', ],
                    
    
                    
    'columns_name'  => [    'id'    => 'id' , 
                            'name'  => '名前', 
                            'email' => '連絡先メールアドレス',
                            'tel'   => '連絡先電話番号',
                            'fax'   => '連絡先FAX番号',
                            'person'    => '担当者',
                            'delivery_date' => '配達希望日',
                            'delivery_name'   => '納品先名',
                            'delivery_tel'      => '電話番号',
                            'delivery_person'   => '担当者',
                            'delivery_postcode' => '郵便番号',
                            'delivery_prefecture' => '都道府県',
                            'delivery_city'   => '市区町村',
                            'delivery_address'   => '住所',

                            'memo' => '備考',
                        ],

    'lock_ids'          => [],
    
    'items' => [ 
                [ 'name' => 'T-MIST 専用ハンドスプレー（容器のみ）', 'amount' => '300ml',   'price' => 500 ],
                [ 'name' => 'T-MIST 次亜塩素酸水入りハンドスプレー', 'amount' => '300ml',   'price' => 1250 ],
                [ 'name' => 'T-MIST 専用空ボトル',                   'amount' => '1000ml',  'price' => 1000 ],
                [ 'name' => 'T-MIST 次亜塩素酸水入り　ボトル',       'amount' => '1000ml',  'price' => 2500 ],
                [ 'name' => 'T-MIST BOX次亜塩素酸水　詰替用',        'amount' => '20l',     'price' => 15000 ],
                [ 'name' => '超音波式噴霧器',                        'amount' => '20畳用',  'price' => 43750 ],

            ],

    'notify_ordered' => [ 'user_ids' => [ 1 ]],

];


