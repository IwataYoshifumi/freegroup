<?php

//  パスワードの設定
//

$validator = [ 
    1 => 'min:8',
    2 => 'min:10',
    // 3 => 'regex:/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+$/',
    // 3 => 'regex:/^(?=.*?[a-Z])(?=.*?\d)[a-z\d]{8,100}+$/',
    // 3 => 'regex:/\A[a-zA-Z\d]{8,100}+\z/',
    3 => 'regex:/^(?=.*?[a-zA-Z])(?=.*?\d)[a-zA-Z\d]{8,100}+$/',
    4 => 'regex:/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,100}+$/',
    ];
    
$error = [ 
    1 => [ 'min' => '８文字以上で入力してください' ],
    2 => [ 'min' => '１０文字以上で入力してください' ],
    3 => [ 'regex' => '英数字それぞれ１種類以上含む８文字以上で入力してください'],
    4 => [ 'regex' => '小文字、大文字、数字それぞれ１種類以上含む８文字以上で入力してください'],
    ];

return [
    'validator' => $validator,
    'error'      => $error, 
];


