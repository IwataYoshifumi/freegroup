<?php

//  Admin モデルの設定
//
return [

    'columns' => [ 'id', 'name', 'kana', 'email', 'password',
                    'zip_code', 'prefecture', 'city', 'address', 'tel', 'fax', 'mobile', 'birth_day', 'sex', 'memo', 
                ],
    'columns_name'  => [    'id'    => 'id' , 
                            'name'  => '名前', 
                            'email' => 'メール',
                            'password' => 'パスワード',

                            'kana'  => 'ヨミカナ',
                            'zip_code' => '郵便番号',
                            'prefecture'    => '都道府県',
                            'city'          => '市区町村',
                            'street'        => '町名・番地',
                            'building'      => '建物名・部屋番号',
                            'address'       => '住所',
                            'tel'           => '固定電話',
                            'fax'           => 'ＦＡＸ',
                            'mobile'        => '携帯電話',
                            'birth_day'     => '誕生日',
                            'sex'           => '性別',
                            'memo'          => '備考',
                            'salseforce_id' => 'セールスフォースＩＤ',
                            'sansan_id'     => 'Sansan ID'
                        ],
                        
    'validation'    => [ 'email' => ['required' => false ],
    
                        ],
    

    // 'locked_ids'          => [ 1,3,4 ],
    
    // 'password_valicator'=> 1,  # config/passwordに設定
    
    // パスワードの設定
    //
    // 'password'  => [ 'validator'  => 3,    // config.password ファイルのなかのバリデーションインジケータ
    //                  'time_limit' => 30,  // パスワードの有効日数
    //                 ],
    
    'salseforce' => [   'enable' => false,
                        'url'   => 'https://204b4774918d433fa1db11319e58d505.vfs.cloud9.ap-northeast-1.amazonaws.com/customer/show/',
                    ],

    'sansan'     => [ 'enable' => true, 
    
                    ],

];


