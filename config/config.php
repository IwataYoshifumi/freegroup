<?php

return [
    'password' => [ 
                    // パスワードの入力規則
                    //
                    'validation' => [   
                                        // 'min:6', 
                                        'min:10', 
                                        // 'regex:/^.*[a-z]+.*$/', 
                                        // 'regex:/^.*[A-Z]+.*$/', 
                                        //'regex:/^.*\d+.*$/',
                                        //'regex:/^.*\W+.*$/',
                                    ],
                        
                    // パスワード変更からの有効日数、0 なら有効期限は無期限
                    'available_period' => 60,    
                    ],

];