<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'パスワードはリセットされました。',
    'sent' => 'パスワード設定のためのメールを送りました',
    'throttled' => 'しばらく待ってください。',
    'token' => 'トークンが不正です。',
    'user' => "メールアドレスが登録されていません",
    
    /*
     *
     *　下記パスワードリセットメールのメッセージ
     *
     */
    'email' => [
            'subject' => '【FreeGroup】 パスワードリセット',
            'line1'  => 'パスワードリセット要求がありましたので、このメールが送信されました。',
            'line2' => 'このパスワードリセット用リンクには有効期限があります。',
            'line3' => 'もしパスワードリセット要求した覚えがなければ、このメールを破棄してください。',
        ],
];
