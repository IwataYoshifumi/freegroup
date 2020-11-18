@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\ApprovalMaster;

@endphp


@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    
                    @include( 'vacation.approvalMaster.menu' )
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    
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
                            <th colspan=2>承認マスター名</th>
                            <th>退社</th>
                            <th class="align-middle">表示数</th></th>
                        </tr>
                        <tr>
                            <td colspan=2>
                                @php
                                    $result = ApprovalMaster::select( [ 'id', 'name' ] )->get()->toArray();
                                    $approval_masters = [ '' => null ];
                                    foreach( $result as $item ) {
                                        $approval_masters[$item['id']] = $item['name'];
                                    }
                                @endphp
                                <div class='row'>
                                    <div class='col-6'>
                                    {{ Form::select( 'master_id', $approval_masters, old( 'master_id', $request->master_id ), 
                                                            [ 'class' => 'form-control', 'id' => 'master_id' ] ) }}
                                        
                                    </div>
                                    <!--
                                    <div class='col-4'>
                                    {{ Form::text( 'master_name', old( 'master_name', $request->master_name ), 
                                                  [ 'id' => 'master_name', 'class' => 'form-control w-5', 'placeholder' => '承認マスター名' ] ) }}
                                    </div>
                                    -->

                                    <div class="col-6">
                                    {{ Form::checkbox( 'search_not_yet_allocate', 1 , $request->search_not_yet_allocate , [ 'id' => 'search_not_yet' ] ) }}
                                    承認マスター未割当
                                    </div>
                                </div>

                                <script>
                                    $('#search_not_yet').change( function() {
                                        if( $(this).prop('checked') ) {
                                            $('#master_name').prop( 'disabled', true );
                                            $('#master_id').prop( 'disabled', true );                                            
                                        } else {
                                            $('#master_name').prop( 'disabled', false );
                                            $('#master_id').prop( 'disabled', false );                                            
                                        }
                                    });
                                    
                                    $('.document').ready( function() {
                                        $('#search_not_yet').change();   
                                    });    
                                </script>
                                
                            </td>
                            

                            <td>{{ Form::select( 'find[retired]', [ 0 => "在職", 1 => "退社" ] , old( 'find[retired]', ( isset( $find['retired'] )) ? $find['retired'] : "" ),
                                                ['class' => 'form-control' ] )  }}</<td>
                            <td>{{ Form::select( 'find[pagination]', [ 5 => 5, 10 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100 ] ,
                                                old( 'find[pagination]', ( isset( $find['pagination'] )) ? $find['pagination'] : "20"  ),
                                                ['class' => 'form-control' ] )  }}</<td>
                        </tr>
                        <tr>
                            <th colspan=3 class='align-left'><button class="btn btn-primary">検索</button></th>
                            <th colspan=3 align='right'><button class="btn btn-primary">検索</button></th>
                        </tr>
         
                    </table>

                    {{ Form::close() }}


                    <hr>

                    @if( Route::currentRouteName() == 'vacation.approvalMaster.selectUsers'  )
                        {{ Form::open( ['url' => route( 'vacation.approvalMaster.allocate' ), 'method' => 'get', 'id' => 'form_select_users' ] ) }}
                        @csrf
                    @elseif( Route::currentRouteName() == 'vacation.approvalMaster.deallocateSelectUsers'  )
                        {{ Form::open( ['url' => route( 'vacation.approvalMaster.deallocated' ), 'method' => 'post', 'id' => 'form_select_users' ] ) }}
                        @csrf
                    @endif

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            @if( Route::currentRouteName() == 'vacation.approvalMaster.selectUsers'  )
                                <th>承認マスター割当</th>
                            @elseif( Route::currentRouteName() == 'vacation.approvalMaster.deallocateSelectUsers' ) 
                                <th>承認マスター<BR>割当解除</th>        
                            @endif
                            <th>社員番号</th>
                            <th>部署名</th>
                            <th>職級</th>
                            <th>名前</th>
                            <th>割当承認マスター名</th>
                        </tr>
                        @foreach( $users as $user ) 
                            @php $id = $user->id; @endphp

                            <tr class="text-center">
                                @if( Route::currentRouteName() == 'vacation.approvalMaster.selectUsers' or 
                                     Route::currentRouteName() == 'vacation.approvalMaster.deallocateSelectUsers' ) 
                                    <td class="wp-auto">
                                    {{ Form::checkbox( "users[$id]", $id, old( "user[$id]" ), ['class' => 'user_checkbox' ] ) }}
                                    </td>
                                @endif
                                <td>{{ $user->code               }}</td>
                                <td>{{ $user->department['name'] }}</td>
                                <td>{{ $user->grade              }}</td>
                                <td>{{ $user->name               }}</td>
                                <td>{{ ( isset( $allocate[$user->id] )) ? $allocate[$user->id] : "" }}</td>
                            </tr>
                        @endforeach
                        <tr>
                        <td colspan=5>{{ $users->appends( [ 'find' => $find ] )->links() }}</td>
                        </tr>
            
                        @if( Route::currentRouteName() == "vacation.approvalMaster.selectUsers" or 
                             Route::currentRouteName() == "vacation.approvalMaster.deallocateSelectUsers" ) 
                            {{ Form::close() }}
                            <tr>
                                <th colspan=9>
                                    @php
                                        if( Route::currentRouteName() == "vacation.approvalMaster.selectUsers" ) {
                                            $submit_button = "承認マスター割当社員　選択完了（承認マスター選択画面へ）";
                                        } elseif( Route::currentRouteName() == "vacation.approvalMaster.deallocateSelectUsers" ) {
                                            $submit_button = "割当解除　社員選択";        
                                        }
                                    @endphp
                                    
                                    {{ Form::number( 'check_users', old( 'check_users' ), [ 'id' => 'check_users' ] ) }}
                                    <button id="check_btn" class="btn btn-success" type='button'>全てチェック</button>
                                    <button id="submit"    class="btn btn-success" type='submit'>{{ $submit_button }}</button>
                                    <div id="checked" data-checked=0></div>
                                </th> 

                                <script>
                                    $('#check_btn').click( function() {
                                        $('.user_checkbox').each( function(){
                                            if( $('#checked').data('checked') == 0 ) {
                                                $(this). prop('checked', true);
                                            } else {
                                                $(this). prop('checked', false);
                                                
                                            }
                                        }) 
                                        if( $('#checked').data('checked') == 0 ) {
                                            $('#checked').data({checked: 1});
                                            $('#check_btn').html('チェックを外す');
                                            $('#check_users').val( 1 );
                                        } else {
                                            $('#checked').data({checked: 0});
                                            $('#check_btn').html('全てチェック');
                                            $('#check_users').val( 0 );
                                        }
                                    });
                                    
                                    $('.user_checkbox').click( function() {
                                        var num = 0;
                                        $('.user_checkbox:checked').each( function() {
                                             num++;
                                        });
                                        $("#check_users").val( num );
                                    });
                                </script>
                            </tr>
                        @endif
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@php
//dd( $find );
#dump( Session::all() );
@endphp




@endsection

