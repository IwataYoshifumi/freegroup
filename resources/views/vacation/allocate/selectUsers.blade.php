@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;

@endphp


@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            @include( 'vacation.allocate.menu' )
            
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
                    {{ Form::hidden( 'SearchQuery', 1 ) }} 
                    @csrf
                    
                    <table class="table table-primary border border-primary align-middle p-1">
                        <tr class=" align-middle" >
                            <th class="align-middle">名前</th>
                            <th class="align-middle">部署</th>
                            <th class="align-middle">役職</th>
                            <th class="align-middle">表示数</th></th>
                        </tr>
                        <tr class="align-middle" >
                            @php
                                $depts = Dept::getArrayforSelect();
                                $grades= User::getArrayForGradeSelcetForm();
                                $years  = Vacation::getArrayForYearSelcetForm();
                                #$years = array();
                            @endphp
                            
                            <td>{{ Form::text( 'find[name]', old( 'Find[name]', ( isset( $find['name'] )) ? $find['name'] : '' ), 
                                ['class' => 'form-control w-20', 'placeholder' => '名前' ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[dept_id]', $depts, old( 'dept_id', ( isset( $find['dept_id'] )) ? $find['dept_id'] : '' ),
                                                ['class' => 'form-control' ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[grade]', $grades, old( 'grade', ( isset( $find['grade'] )) ? $find['grade'] : '' ),
                                                    ['class' => 'form-control' ] ) }}</td>
                            <td>{{ Form::select( 'find[pagination]', [ 10 => 10, 30 => 30, 50 => 50, 100 => 100 ] ,
                                                old( 'find[pagination]', ( isset( $find['pagination'] )) ? $find['pagination'] : 10 ),
                                                ['class' => 'form-control' ] )  }}</<td>

                        </tr>
                        <tr>
                            <th colspan=2>入社年月日</th>
                            <th colspan=2>割当年度</th>
                            
                        </tr>
                        <tr>

                            <td colspan=2>
                                        <div class='row'>
                                            <div class='col'>
                                            {{ Form::date( 'find[join_date][start]', 
                                                        old( 'find[join_date][start]', ( isset( $find['join_date']['start'] )) ? $find['join_date']['start'] : '' ), 
                                                        [ 'class' => 'form-control w-45' ] ) }}</div>
                                            <div class='col w-5'>～</div>
                                            <div class='col'>
                                            {{ Form::date( 'find[join_date][end]', 
                                                        old( 'find[join_date][end]', ( isset( $find['join_date']['end'] )) ? $find['join_date']['end'] : '' ), 
                                                        [ 'class' => 'form-control w-45' ] ) }}</div>
                                        </div>
                            </td>
                            <td colspan=2>
                                 {{ Form::select( 'find[year]', $years, old( 'find[year]', ( isset( $find['year'] )) ? $find['year'] : '' ), 
                                                [ 'class' => 'form-control w-1' ] ) }}
                                 {{ Form::select( 'find[allocatedOrNot]', ['未割当' => '未割当', '割当済み' => '割当済み' ], 
                                                 old( 'find[allocatedOrNot]', ( isset( $find['allocatedOrNot'] )) ? $find['allocatedOrNot'] : '' ),
                                                [ 'class' => 'form-control w-1' ] ) }}
                            </td>
                        <tr>
                            <th colspan=3 class='align-left'><button class="btn btn-primary">検索</button></th>
                            <th colspan=3 align='right'><button class="btn btn-primary">検索</button></th>
                        </tr>
         
                    </table>
                    {{ Form::close() }}


                    <hr>
                    @if( Route::currentRouteName() == 'vacation.allocate.select' ) 
                        <!-- 有給割当フォーム -->
                        {{ Form::open( ['url' => route( 'vacation.allocate.create' ), 'method' => 'get', 'id' => 'form_select_users' ] ) }}
                        @csrf
                    @endif
                    

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            <th>アクション</th>
                            <th>部署名</th>
                            <th>職級</th>
                            <th>名前</th>
                            <th>メール</th>
                            <th>中途・新卒</th>
                            <th>入社年月</th>
                            <th>入社年月日</th>
                            <th>退社</th>
                        </tr>
                        @foreach( $users as $user ) 
                            @php $id = $user->id; @endphp

                            <tr class="text-center">
                                <td class="wp-auto">
                                    @if( Route::currentRouteName() == 'vacation.allocate.select' ) 
                                        {{ Form::checkbox( "users[$id]", $id, old( "user[$id]" ), ['class' => 'user-checkbox' ] ) }}
                                    @endif
                                </td>
                                <td>{{ $user->department['name'] }}</td>
                                <td>{{ $user->grade              }}</td>
                                <td>{{ $user->name               }}</td>
                                <td>{{ $user->email              }}</td>
                                <td>{{ $user->carrier            }}</td>
                                <td>{{ $user->year               }}</td>
                                <td>{{ $user->join_date          }}</td>
                                <td>@if( $user->retired ) 退社  @endif </td>
                            </tr>
                        @endforeach
                         {{ Form::close() }}
                        
            
                        @if( Route::currentRouteName() == "vacation.allocate.select" ) 
                            <!--　有給割当フォーム    -->                
                            <tr>
                                <th colspan=9>
                                    <a id="check_btn"  class="btn btn-primary text-white">全てチェック</a>
                                    <a id="submit_btn" class="btn btn-primary text-white">有給割当</a>
                                    <div id="checked" data-checked=0></div>
                                </th> 
                               
                                <script>
                                
                                    //　全てチェック・チェックを外すボタン
                                    //
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
                                    
                                    //　有給割当ボタン（submit）
                                    //
                                    //  従業員チェックボックスが選択されているか確認
                                    //
                                    $('#submit_btn').click( function() {
                                        var checked_users = 0;
                                        $('.user-checkbox:checked').each( function() {
                                           checked_users++; 
                                        }); 
                                        console.log( checked_users );
                                        if( checked_users >= 1 ) {
                                           $('#form_select_users').submit();   
                                        } else {
                                            window.alert('従業員を選択してください')
                                        }
                                    });

                                </script>
                            </tr>
                        @endif
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

