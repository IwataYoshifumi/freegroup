
@php
use Carbon\Carbon;
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use Illuminate\Support\Facades\Auth; 

@endphp  

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'SearchQuery', 1 ) }}
    @csrf
    
    <div class="container border border-primary bg-light shadow align-middle p-3">
        <div class="row">
            <div class="col-12 d-none d-lg-block">
                <div class="row" >
                    <div class="col-3 m-2">名前</div>
                    <div class="col-3 m-2">部署</div>
                    <div class="col-2 m-2">役職</div>
                </div>
            </div>
            <div class="row container">
                @php
                    if( optional( auth( 'user' ))->user() ) { 
                        $auth = auth('user')->user(); 
                    } elseif( optional(auth('admin'))->user() ) {
                        $auth = auth('admin')->user(); 
                    } else {
                        abort( 403, 'parts_find.blade: 認証エラー' );
                    }
                    $grades= User::getArrayForGradeSelcetForm();
                @endphp
            
                <div class="col-12 d-lg-none m-2">名前</div>
                @if( $auth->is_user() and $auth->browsing() == "自分のみ" ) 
                    {{ Form::hidden( 'find[user_id]', $auth->id ) }} <div class="col-7 col-lg-3">本人のみ検索可能</div>
                @else
                    {{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                                    ['class' => 'form-control col-7 col-lg-3 m-2', 'placeholder' => '名前' ] ) }}
                @endif
            
                <div class="col-12 d-lg-none m-2">部署</div>

                @if( $auth->is_user() and $auth->browsing() == "自分のみ" ) 
                    <div class="col-7 col-lg-3">本人のみ検索可能</div>
                @else
                    @php
                        if( $auth->is_admin() or ( $auth->is_user() and $auth->browsing() == "全社" )) {
                            $depts = Dept::getArrayforSelect();
                        } else {
                            //　部内のみ検索可能
                            // 
                            $depts = Dept::getArrayforSelect( [ 'id' => $auth->department->id ], TRUE );
                        }
                    @endphp
                    {{ Form::select( 'find[dept_id]', $depts, old( 'find[dept_id]', ( isset( $find['dept_id'] )) ? $find['dept_id'] : null ),
                                        ['class' => 'form-control col-7 col-lg-3 m-2' ] ) }}
                @endif

                <div class="col-12 col-lg-2 d-lg-none m-2">役職</div>
                {{ Form::select( 'find[grade]', $grades, old( 'find[grade]', ( isset( $find['grade'] )) ? $find['grade'] : null ),
                            ['class' => 'form-control col-7 col-lg-2 m-2' ] ) }}

                </div>


            @php
                $status = [ '承認待ち'=>'承認待ち', '承認'=>'承認', '休暇取得完了' => '休暇取得完了', '取り下げ' => '取り下げ', '却下' => '却下' ];
                if( empty( $find['status'] )) { $find['status'] = array(); }
                $i=0;
            @endphp
    
            <div class="col-12 d-lg-none"></div>
            <div class="d-none d-lg-block">    
                <div class='col-11'>
                    <div class="row">
                        <div class="col-3 col-lg-12 m-2"
                            data-toggle="collapse"
                            data-target="#status_modal"
                            aria-expand="false"
                            aria-controls="status_modal">ステータス
                        </div>
                        <div class="col-7 col-lg-12 m-2 container collapse" id="status_modal">
                            <div class="row">
                                @foreach( $status as $s )
                                    <div class="col ml-1">
                                        @php 
                                            ( in_array( $s, $find['status'] )) ? $checked = true : $checked = false
                                        @endphp 
                                            {{ Form::checkbox( "find[status][$i]", $s, $checked, [ 'class' => 'form-check-input shadow'] ) }}
                                            <div class='form-check-label'>{{ $s }}</div>
                                        @php $i++ @endphp
                                    </div>
                                @endforeach
                                        
                            </div>
                        </div>
                        <div class="col-3 col-lg-12 m-2"
                            data-toggle="collapse"
                            data-target="#show_modal"
                            aria-expand="false"
                            aria-controls="show_modal">表示項目
                        </div>
                        <div  class="col-7 col-lg-12 m-2 container collapse" id="show_modal">
                            @php
                                $shows = ( isset( $find['show'] )) ? optional( $find )['show'] : [];
                                $array_show = [ '部署名', 'ステータス', '休暇種別', '休暇理由' ];
                            @endphp
                            <div class="row">
                                @foreach( $array_show as $s )
                                    <div class="col ml-1">
                                        @php 
                                            ( in_array( $s, $shows )) ? $checked = true : $checked = false;
                                        @endphp
                                        {{ Form::checkbox( "find[show][$s]", $s, $checked, [ 'class' => 'form-check-input shadow'] ) }}
                                        <div class='form-check-label'>{{ $s }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- 検索ボタン -->
            <div class="col-12 container">
                <div class="row">
                    <button class="btn btn-primary">検索</button>
                </div>
            </div>
            
        </div>
    </div>

{{ Form::close() }}
<div class="m-3"></div>

@php

@endphp 

