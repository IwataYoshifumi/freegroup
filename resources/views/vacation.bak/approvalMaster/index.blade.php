@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;;
use App\Models\Vacation\ApprovalMaster;
use App\Models\Vacation\ApprovalMasterList;

if( is_array( $request->find )) {
    $find = $request->find;
} else {
    $find = array();
}

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

                    {{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
                    {{ Form::hidden( 'SearchQuery', 1 ) }} 
                    @csrf
                    
                    
                    <table class="table table-primary border border-primary align-middle p-1">
                        <tr class=" align-middle" >
                            <th class="align-middle">承認マスター名</th>
                            <th><div>
                                {{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                                ['class' => 'form-control w-5', 'placeholder' => '承認マスター名' ] ) }}
                                
                                <div class="m-2">全文一致：
                                    {{ Form::checkbox( 'find[name_strict_search]', 1 , ( isset( $find['name_strict_search'] )) ? TRUE : FALSE ) }}
                                </div>
                                </div>
                            </th>
                            
                        </tr>
                        <tr>
                            <th colspan=3 class='align-left'><button class="btn btn-primary">検索</button></th>
                        </tr>
         
                    </table>

                    {{ Form::close() }}


                    <hr>

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            <th>&nbsp;</th>
                            <th>マスター名</th>
                            <th>備考</th>
                            <th>承認者</th>
                        </tr>
                        @foreach( $masters as $master ) 
                            @php
                                $lists = $master->approvalMasterLists;
                                
                                    
                            @endphp
                            <tr class="text-center">
                                <td class="wp-auto">
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.approvalMaster.show', $master->id ) }}">詳細</a>
                                    <a class="show-btn btn btn-sm btn-outline-primary text-primary" href="{{ route( 'vacation.approvalMaster.edit', $master->id ) }}">変更</a>
                                </td>
                                <td>{{ $master->name  }}</td>
                                <td>{{ $master->memo  }}</td>
                                <td>
                                    @foreach( $lists as $list ) 
                                        ( {{ $list->approver->department->name }} )
                                        {{ $list->approver->name }}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        
            
                        @if( Route::currentRouteName() == "allocate.select" ) 
                        @endif
                    </table>
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

