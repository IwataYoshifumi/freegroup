@extends('layouts.app')

@php
use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyHelper;
use App\myHttp\GroupWare\Models\RoleGroup;

$route_name = Route::currentRouteName();

$confirms = [ 
    'ロールグループの初期値設定があることを確認しました', 
    '管理権限のあるアクセスリストを持たないユーザにアクセスリストを作成します',
    'スケジュール作成権限のあるカレンダーがなければ、当該ユーザの公開カレンダーを作成します',
    'この処理は、データベースのユーザーテーブル「users」に直接データをインポートした時のみ必要です。',
    '上記の通り、通常はFreeGroupを最初に立ち上げた直後に１回のみ実行すれば十分です。',
            ];

@endphp

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            @include( 'groupware.user.menu_button' )
            
            <div class="card">
                <div class="card-header">{{ config( $route_name ) }}</div>
                
                @include( 'layouts.error' )
                
                @include( 'layouts.flash_message' )

                <div class="card-body">
                    <div class="row">
                        <span class="alert-danger col-12">この操作はFreeGroupインストール直後に１回行うだけで基本的にOkです。</span>
                        <span class="alert-danger col-12">またSQLのユーザテーブル「users」に直接SQLでデータをインポートした場合には、この操作が必要です。</span>
                    </div>
                    <hr>
                    
                    {{ Form::open( [ 'route' => $route_name, 'method' => 'POST', 'id' => 'init_form' ]) }}                    
                        @csrf
                        {{ Form::hidden( 'confirms_num', count( $confirms )) }}
                        @foreach( $confirms as $i => $confirm )
                            @php
                                $id = "confirm_" . $i;
                            @endphp
                            <label for='{{ $id }}'>{{ $confirm }}</label>
                            <input type="checkbox" name="confirms[{{ $i }}][init]" value=1 class='w-90 checkboxradio' id="{{ $id }}">
                        @endforeach
                        <hr>
                        <a class="btn btn-danger text-white" id="submit_btn">DB初期化実行</a>                    
                    {{ Form::close() }}
                    <script>
                        $('#submit_btn').on( 'click', function() {
                            console.log( 'aaa' );
                            $('#init_form').submit(); 
                        });
                    </script>

                {{ BackButton::form() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection