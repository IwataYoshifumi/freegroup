@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;

@endphp


@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'vacation.users.menu' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    {{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
                    {{ Form::hidden( 'SearchQuery', 1 ) }} 
                    @csrf
                    
                    <table class="table table-primary border border-primary align-middle p-1">
                        <tr class=" align-middle" >
                            <th class="align-middle">社員番号</th>
                            <th class="align-middle">名前</th>
                            <th class="align-middle">メール</th>
                            <th class="align-middle">部署</th>
                            <th class="align-middle">役職</th>

                        </tr>
                        <tr class="align-middle" >
                            @php
                                $depts = Dept::getArrayforSelect();
                                $grades= User::getArrayForGradeSelcetForm();
                            @endphp
                            <td>{{ Form::text( 'find[code]', old( 'find[code]', ( isset( $find['code'] )) ? $find['code'] : "" ), 
                                ['class' => 'form-control w-5', 'placeholder' => '社員番号' ] ) }}
                            </td>
                            <td>{{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                                ['class' => 'form-control w-15', 'placeholder' => '名前' ] ) }}
                            </td>
                            <td>{{ Form::text( 'find[email]', old( 'find[email]', ( isset( $find['email'] )) ? $find['email'] : null  ), 
                                ['class' => 'form-control w-15', ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[dept_id]', $depts, old( 'find[dept_id]', ( isset( $find['dept_id'] )) ? $find['dept_id'] : null ),
                                                ['class' => 'form-control' ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[grade]', $grades, old( 'find[grade]', ( isset( $find['grade'] )) ? $find['grade'] : null ),
                                                    ['class' => 'form-control' ] ) }}</td>


                        </tr>
                        <tr>
                            <th colspan=2>入社年月日</th>
                            <th>新卒・中途</th>
                            <th>退社</th>
                            <th class="align-middle">表示数</th></th>
                        </tr>
                        <tr>

                            <td colspan=2>
                                        <div class='row'>
                                            <div class='col'>
                                            {{ Form::date( 'find[join_date][start]', 
                                                        old( 'find[join_date][start]', ( isset( $find['join_date']['start'] )) ? $find['join_date']['start'] : "" ), 
                                                        [ 'class' => 'form-control w-45' ] ) }}</div>
                                            <div class='col w-5'>～</div>
                                            <div class='col'>
                                            {{ Form::date( 'find[join_date][end]', 
                                                        old( 'find[join_date][end]', ( isset( $find['join_date']['end'] )) ? $find['join_date']['end'] : "" ), 
                                                        [ 'class' => 'form-control w-45' ] ) }}</div>
                                        </div>
                            </td>
                            <td>{{ Form::select( 'find[carrier]', [ '' => '-', '新卒' => '新卒', '中途' => '中途' ], 
                                    old( 'find[carrier]', ( isset( $find['carrier'] )) ? $find['carrier'] : "" ), [ 'class' => 'form-control' ] ) }}
                                
                                
                            </td>
                            <td>{{ Form::select( 'find[retired]', [ 0 => "在職", 1 => "退社" ] , old( 'find[retired]', ( isset( $find['retired'] )) ? $find['retired'] : "" ),
                                                ['class' => 'form-control' ] )  }}</<td>
                            <td>{{ Form::select( 'find[pagination]', [ 10 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100 ] ,
                                                old( 'find[pagination]', ( isset( $find['pagination'] )) ? $find['pagination'] : ""  ),
                                                ['class' => 'form-control' ] )  }}</<td>
                        </tr>
                        <tr>
                            <th colspan=2>閲覧権限</th>
                            <!--<th>管理者・一般ユーザ</th>-->
                        </tr>
                        <td colspan=2>
                            <div class='row'>
                                @foreach( config( 'vacation.constant.authority.browsing' ) as $browsing ) 
                                    @php
                                    ( ! empty( $find['browsing'] ) && in_array( $browsing, $find['browsing'], true )) ? $checked = true : $checked = false;
                                    @endphp
                                    <div class='col m-2'>
                                    {{ Form::checkbox( "find[browsing][$browsing]", $browsing, $checked ) }}&nbsp;{{ $browsing }} 
                                     </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @php
                            $admin_select = [ '' => '', true => '管理者', false => '一般ユーザ' ];
                            @endphp
                            <!--{{ Form::select( "find[admin]", $admin_select, old( "find[admin]", ( isset( $find['admin'] )) ? $find['admin'] : '' ) , [ 'class' => 'form-control' ] ) }}-->
                            
                        </td>

                        <tr>
                            <th colspan=3 class='align-left'><button class="btn btn-primary">検索</button></th>
                            <th colspan=3 align='right'><button class="btn btn-primary">検索</button></th>
                        </tr>
         
                    </table>
                    <table class='table table-border'>
                        <tr><th>表示項目</th><td><div class='row'>
                        @php 
                            $show_items   = [ '社員番号', '部署名', '職級', '閲覧権限','メール', '入社年月日', '退社' ];
                            //dump( $find );
                        @endphp
                        @foreach( $show_items as $item ) 
                            @php
                            ( ! empty( $find['show_item'] ) && in_array( $item, $find['show_item'], true )) ? $checked = 'checked' : $checked = "" ;
                            @endphp
                            <div class='col'>{{ Form::checkbox( "find[show_item][$item]", $item, $checked ) }}&nbsp;{{ $item }}</div>
                        @endforeach
                        </div></td>
                        
                        
                        </tr>
                    </table>
                    
                    {{ Form::close() }}


                    <hr>
                    @if( Route::currentRouteName() == 'allocate.select' ) 
                        <!-- 有給割当フォーム -->
                        {{ Form::open( ['url' => route( 'allocate.create' ), 'method' => 'get', 'id' => 'form_select_users' ] ) }}
                        @csrf
                    @endif

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            <th>アクション</th>
                            @if( ! empty( $find['show_item']['管理者']     )) <th>管理者</th>     @endif
                            @if( ! empty( $find['show_item']['社員番号']   )) <th>社員番号</th>     @endif
                            <th>名前</th>
                            @if( ! empty( $find['show_item']['部署名']     )) <th>部署名</th>     @endif
                            @if( ! empty( $find['show_item']['職級']       )) <th>職級</th>       @endif
                            @if( ! empty( $find['show_item']['メール']     )) <th>メール</th>     @endif
                            @if( ! empty( $find['show_item']['閲覧権限']   )) <th>閲覧権限</th>   @endif
                            @if( ! empty( $find['show_item']['入社年月日'] )) <th>入社年月日</th> @endif
                            @if( ! empty( $find['show_item']['退社']       )) <th>退社</th> 　　　@endif
                            
                        </tr>
                        @foreach( $users as $user ) 
                            @php 
                                $id = $user->id; 
                                if( $user->is_admin() ) { 
                                    $bg_style = 'bg-color-danger text-color-white'; 
                                } else {
                                    $bg_style = '';
                                }
                            @endphp

                            <tr class="text-center">
                                <td class="wp-auto {{ $bg_style }}">
                                    @if( Route::currentRouteName() == 'allocate.select' ) 
                                        {{ Form::checkbox( "users[$id]", $id, old( "user[$id]" ), ['class' => 'user-checkbox' ] ) }}
                                        
                                    @else
                                        <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.user.detail', $user->id ) }}">詳細</a>
                                        <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.user.edit', $user->id ) }}">変更</a>
                                    @endif
                                </td>
                                @if( ! empty( $find['show_item']['管理者']     )) <td>@if( $user->is_admin() ) 管理者  @endif </td> @endif
                                @if( ! empty( $find['show_item']['社員番号']     )) <td>{{ $user->code             }}</td> @endif
                                <td><a href='{{ route( 'vacation.user.show', $user->id ) }}'>
                                    {{ $user->name               }}</a></td>
                                @if( ! empty( $find['show_item']['部署名']     )) <td>{{ $user->department['name'] }}</td> @endif
                                @if( ! empty( $find['show_item']['職級']       )) <td>{{ $user->grade              }}</td> @endif
                                @if( ! empty( $find['show_item']['メール']     )) <td>{{ $user->email              }}</td> @endif
                                @if( ! empty( $find['show_item']['閲覧権限']   )) <td>{{ $user->browsing           }}</td> @endif
                                @if( ! empty( $find['show_item']['入社年月日'] )) <td>{{ $user->join_date          }}</td> @endif
                                @if( ! empty( $find['show_item']['退社']       )) <td>@if( $user->retired ) 退社  @endif </td> @endif
                                
                            </tr>
                        @endforeach
                        
            
                        @if( Route::currentRouteName() == "allocate.select" ) 
                            <!--　有給割当フォーム    -->                
                            <tr>
                                <th colspan=9>
                                    <button id="check_btn" class="btn btn-primary">全てチェック</button>
                                    <button id="submit"    class="btn btn-primary">有給割当</button>
                                    <div id="checked" data-checked=0></div>
                                </th> 
                                {{ Form::close() }}
                                <script>
                                    $('#check_btn').click( function() {
                                        $('.user-checkbox').each( function(){
                                            if( $('#checked').data('checked') == 0 ) {
                                                $(this). prop('checked', true);
                                            } else {
                                                $(this). prop('checked', false);
                                                
                                            }
                                        }) 
                                        if( $('#checked').data('checked') == 0 ) {
                                            $('#checked').data({checked: 1});
                                            $('#check_btn').html('チェックを外す');
                                        } else {
                                            $('#checked').data({checked: 0});
                                            $('#check_btn').html('全てチェック');
                                        }
                                    });
                                    
                                    $('#submit').click( function() {
                                        var checked_users = 0;
                                        $('.user-checkbox').each( function() {
                                           if( $(this).val() >= 1 ) { checked_user++; }
                                        }); 
                                        if( checked_users >= 1 ) {
                                            $('#form_select_users').submit();   
                                        } else {
                                            
                                        }
                                    

                                    });
                                    
                                    

                                </script>
                            </tr>
                        @endif
                    </table>
                    @php
                        
                    
                    @endphp
                    
                    
                    {{ $users->appends( [ 'find' => $find, 'SearchQuery' => 1] )->links() }}
                </div>
            </div>
        </div>
    </div>
</div>




@php
#dump( $find );
@endphp




@endsection

