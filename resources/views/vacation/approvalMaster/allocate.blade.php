@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;

use App\Http\Helpers\BackButton;

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

                   <hr>


                    {{ Form::open( ['url' => route( 'vacation.approvalMaster.allocated' ), 'method' => 'post', 'id' => 'form_select_users' ] ) }}
                    @csrf

                    <table class='table table-bordered table-success'>
                        <tr>
                            <th>割当申請マスター
                                <div class='row'>
                                    {{ Form::text( 'search_master', old( 'search_master' ), 
                                                   [ 'class' => 'form-control col-3 m-2', 'placeholder' => 'マスター検索', 'id' => 'master_search' ] ) }}    
                                    {{ Form::select( 'master', array(), old( 'master' ),    [ 'class' => 'form-control col-4 m-2', 'id' => 'master_select', 'required' ] )    }}
                                    <button class='btn btn-success'>承認マスター割当</button>
                                </div>
                                <script language='JavaScript'>
                                    $('#master_search').change( function() {
                                        var search = $(this).val();
                                        var url    = "{{ route( 'vacation.json.getApprovalMaster' ) }}";
                                        console.log( search );
                                        $.ajax( url, {
                                            ttype: 'get',
                                            data:  { name : search },
                                            dataType: 'json',
                                        }).done( function( data ) {
                                            console.log( data ); 
                                            $("#master_select").children().remove();
                                            $("#master_select").append($("<option>").val("").text("---"));
                                            $.each( data, function( id, name ) {
                                               $("#master_select").append($("<option>").val(id).text(name)); 
                                            });
                                        });                                    
                                    });
                                    
                                    $('.document').ready( function() {
                                        var search = $(this).val();
                                        var url    = "{{ route( 'vacation.json.getApprovalMaster' ) }}";
                                        console.log( search );
                                        $.ajax( url, {
                                            ttype: 'get',
                                            data:  { name : search },
                                            dataType: 'json',
                                        }).done( function( data ) {
                                            console.log( data ); 
                                            $("#master_select").children().remove();
                                            $("#master_select").append($("<option>").val("").text("---"));
                                            $.each( data, function( id, name ) {
                                               $("#master_select").append($("<option>").val(id).text(name)); 
                                            });
                                        });                                    
                                    });
                                </script>
                            </th>
                        </tr>
                    </table>
                    
                    <div class='col h5'>承認マスター割当　社員</div>

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            <th>部署名</th>
                            <th>職級</th>
                            <th>社員番号</th>
                            <th>名前</th>
                            <th>割当承認マスター名</th>
                        </tr>
                        @foreach( $users as $user ) 
                            {{ Form::hidden( "users[".$user->id."]", $user->id ) }}
                            <tr class="text-center">
                                <td>{{ $user->department['name'] }}</td>
                                <td>{{ $user->grade              }}</td>
                                <td>{{ $user->code               }}</td>
                                <td>{{ $user->name, $user->id    }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        
            
                            
                    </table>
                    {{ Form::close() }}
                    {{ BackButton::form() }}
                    @php
                        
                    
                    @endphp
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>




@php
//dd( $find );
@endphp




@endsection

