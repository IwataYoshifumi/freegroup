<?php

//  Admin モデルの設定
//
return [

    'columns' => [ 'id', 'name', 'email', 'password', 'retired', 'date_of_retired' ],
    'columns_name'  => [    'id'    => 'id' , 
                            'name'  => '名前', 
                            'email' => 'メール',
                            'password' => 'パスワード',
                            'retired'   => '退社',
                            'date_of_retired' => '退社日',
                        ],

    'locked_ids'          => [ 3,4 ],
    
    // パスワードの設定
    //
    'password_valicator'=> 3,  # config/passwordに設定
    'password'  => [ 'validator'  => 1,    // config.password ファイルのなかのバリデーションインジケータ
                     'time_limit' => 30,  // パスワードの有効日数
                    ],
];


