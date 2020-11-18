@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\MyForm;

$names = [ 'SecretKey', 
           'CompanyName',
           'CompanyNameReading',
           'DepartmentName',
           'Title',
           'LastName',
           'FirstName',
           'LastNameReading',
           'FirstNameReading',
           'PostalCode1',
           'Prefecture',
           'City',
           'Address',
           'Building',
           'Tel1',
           'Fax1',
           'Mobile1',
           'Email1',
           'Url1',
           'Memo',
           'TagNames', 
           ];

$labels = ['SecretKey'             => "アクセスキー", 
           'CompanyName'           => "会社名", 
           'CompanyNameReading'    => "会社名（カナ）",
           'DepartmentName'        => "部署名",
           'Title'                 => "役職",
           'LastName'              => "氏",
           'FirstName'             => "名",
           'LastNameReading'       => "氏（カナ）",
           'FirstNameReading'      => "名（カナ）",
           'PostalCode1'           => "郵便番号",
           'Prefecture'            => "都道府県",
           'City'                  => "市区町村",
           'Address'               => "住所",
           'Building'              => "建物名・部屋号",
           'Tel1'                  => "電話番号",
           'Fax1'                  => "ファックス番号",
           'Mobile1'               => "携帯番号",
           'Email1'                => "メールアドレス",
           'Url1'                  => "ホームページ",
           'Memo'                  => "メモ",
           'TagNames'              => "タグ", 
           ];

$types =  ['SecretKey'             => "hidden", 
           'CompanyName'           => "", 
           'CompanyNameReading'    => "",
           'DepartmentName'        => "",
           'Title'                 => "",
           'LastName'              => "",
           'FirstName'             => "",
           'LastNameReading'       => "",
           'FirstNameReading'      => "",
           'PostalCode1'           => "",
           'Prefecture'            => "",
           'City'                  => "",
           'Address'               => "",
           'Building'              => "",
           'Tel1'                  => "number",
           'Fax1'                  => "",
           'Mobile1'               => "",
           'Email1'                => "email",
           'Url1'                  => "",
           'Memo'                  => "",
           'TagNames'              => "", 
           ];
           
$defaults=['SecretKey'             => config( 'sansan.secret_key' ),
           'CompanyName'           => "", 
           'CompanyNameReading'    => "",
           'DepartmentName'        => "",
           'Title'                 => "",
           'LastName'              => "",
           'FirstName'             => "",
           'LastNameReading'       => "",
           'FirstNameReading'      => "",
           'PostalCode1'           => "",
           'Prefecture'            => "",
           'City'                  => "",
           'Address'               => "",
           'Building'              => "",
           'Tel1'                  => "",
           'Fax1'                  => "",
           'Mobile1'               => "",
           'Email1'                => "",
           'Url1'                  => "",
           'Memo'                  => "",
           'TagNames'              => config( 'sansan.tag_names' ), 
           ];

$form_classes = [
           'CompanyName'           => "col-8", 
           'CompanyNameReading'    => "col-8",
           'DepartmentName'        => "col-8",
           'Title'                 => "col-5",
           'LastName'              => "col-10",
           'FirstName'             => "col-10",
           'LastNameReading'       => "col-10",
           'FirstNameReading'      => "col-10",
           'PostalCode1'           => "col-5",
           'Prefecture'            => "col-5",
           'City'                  => "col-8",
           'Address'               => "col-12",
           'Building'              => "col-12",
           'Tel1'                  => "col-8",
           'Fax1'                  => "col-8",
           'Mobile1'               => "col-8",
           'Email1'                => "",
           'Url1'                  => "",
           'Memo'                  => "",
           'TagNames'              => "WebImportAPI", 
           ];

$bk = "md";


$confirms = [ 'Email1' => true ];

$departments = [ "総務部", "営業部", "人事部", "技術部", "工務部", "保守運用部", "営業開発部" ];
$interests   = [ "そうじ", "旅行", "読書", "映画", "ゴルフ", "お酒" ];

$values = [ 'department' => $departments,
            'retired'    => [ "退職" => "退職" ],
            'sex'    => [ "男" => "男", "女" => "女" ],
            'interests' => $interests,
        ];
        


@endphp

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10">
                <div class="card">

                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    {{ Form::open( [ 'method' => 'POST', 'route' => 'sansan.api' ] ) }}
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
