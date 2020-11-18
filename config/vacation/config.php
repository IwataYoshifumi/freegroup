<?php

return [

    //  有給休暇申請システムの設定
    //

    // 時間有給を使う場合はtrue
    //
    'is_valid_hourly_paidleave'    => true,
    // 'is_valid_hourly_paidleave'    => false,
    
    //　時間有給の設定
    //
    
    
    'hourly_paidleave' => [ 'min' => 9,   // 最低時刻
                            'max' => 20,  // 最高時刻
                            'step' => 3600,    // 入力ステップ（秒単位）
                            // 'unit' => 1 / 8 / 1, // １時間単位   0.125
                            // 'unit' => 1 / 8 / 2, // ３０分単位 0.0625
                            'unit' => 1 / 8 / 4, // １５単位  0.03125
        ],
    

];
