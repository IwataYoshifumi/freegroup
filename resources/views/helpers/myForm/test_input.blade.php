@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\MyForm;
$names   = [ 'name', 'email', 'department', 'post_code', 'city', 'address', 'tel', 'fax','birth_day', 'interests','sex', 'memo',  ];

$types  = [ 'email'        => 'email', 
            'birth_day'    => 'date',
            'memo'         => 'textarea',
            'address'      => 'text', 
            'department' => 'select',
            'interests'  => 'checkbox',
            'sex'        => 'radio',
            
];


$labels  = [ 'name'  => '名前', 
             'email' => '連絡先メールアドレス',
             'post_code' => '郵便番号',
             'city'       => '市区町村',
             'address'      => '住所',
             'tel'   => '電話番号',
             'fax'   => 'FAX番号',
             'birth_day' => '生年月日',
             'sex' => '性別',
            'memo' => '備考',
            'department' => '部署',
            'interests' => '趣味',
                        ];


$bk = "md";

$form_classes = [ 
        'post_code' => 'col-5', 
          'tel'     => "col-10 col-$bk-6",
          'fax'    => "col-10 col-$bk-6",
          'email' => 'col-8',
          'address' => 'col-12',
          'department' => 'col-10',
          'birth_day' => "col-12 col-$bk-8",
            ];

$confirms = [ 'email' => true ];

$departments = [ "総務部", "営業部", "人事部", "技術部", "工務部", "保守運用部", "営業開発部" ];
$interests   = [ "そうじ", "旅行", "読書", "映画", "ゴルフ", "お酒" ];

$values = [ 'department' => $departments,
            'retired'    => [ "退職" => "退職" ],
            'sex'    => [ "男" => "男", "女" => "女" ],
            'interests' => $interests,
        ];
        
$defaults = [ 'retired' => [ '退職' => '' ],


        ];

@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <div class="card">

                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    {{ Form::open( [ 'method' => 'POST', 'route' => 'mail_order.store' ] ) }}
                        @csrf

                        <div class="card-body">
                            <div class="w-100">お客様情報</div>
                            <div class="container m-1">
                                
                                {{ MyForm::input( [ 'names'     => $names, 
                                                    'labels'    => $labels, 
                                                    'values'    => $values,
                                                    'types'     => $types, 
                                                    'breakpoint'=> $bk,
                                                    'form_classes'  => $form_classes,
                                                    'confirms'  => $confirms,
                                                    'defaults'   => $defaults,
                                                    ] ) }}
                     
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">登録</button>
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@php 
#dump( request()->all() );
@endphp
@endsection
