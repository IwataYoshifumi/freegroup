<?php

return [
    'password' => [ 
                    // パスワードの入力規則
                    //
                    'validation' => [   
                                        'min:6', 
                                        // 'min:10', 
                                        // 'regex:/^.*[a-z]+.*$/', 
                                        // 'regex:/^.*[A-Z]+.*$/', 
                                        //'regex:/^.*\d+.*$/',
                                        //'regex:/^.*\W+.*$/',
                                    ],
                        
                    // パスワード変更からの有効日数、0 なら有効期限は無期限
                    'available_period' => 60,    
                    ],
    
    'paginations'   => ( is_debug() ) ? [ 3 => 3, 5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100 ] 
                                      : [ 20 => 20, 50 => 50, 100 => 100 ],


];