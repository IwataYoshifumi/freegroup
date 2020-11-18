@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;

if( is_array( $request->find )) {
    $find = $request->find;
} else {
    $find = array();
}

$departments = Dept::select( 'id', 'name' )->get();
foreach( $departments as $d ) {
    $depts[$d->id] = $d->name;
}

@endphp


@section('content')


<div class="container w-100">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'vacation.allocate.menu' )
            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

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
                                $depts  = Dept::getArrayforSelect();
                                $grades = User::getArrayForGradeSelcetForm();
                                $years  = Vacation::getArrayForYearSelcetForm();
                            @endphp
                            
                            <td>{{ Form::text( 'find[user_name]', old( 'find[user_name]', $request->find['user_name'] ), 
                                ['class' => 'form-control w-20', 'placeholder' => '名前' ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[dept_id]', $depts, old( 'find[dept_id]', $request->find['dept_id'] ),
                                                ['class' => 'form-control' ] ) }}
                            </td>
                            <td>{{ Form::select( 'find[grade]', $grades, old( 'grade', $request->find['grade'] ),
                                                    ['class' => 'form-control' ] ) }}</td>
                            <td>{{ Form::select( 'find[pagination]', [ 10 => 10, 30 => 30, 50 => 50, 100 => 100 ] ,
                                                old( 'find[pagination]', $request->find['pagination'] ),
                                                ['class' => 'form-control' ] )  }}</<td>

                        </tr>
                        <tr>
                            <th colspan=2>割当年度</th>
                        </tr>
                        <tr>

                            <td>
                                        <div class='row'>
                                            <div class='col'>

                                            {{ Form::select( 'find[year]', $years, old( 'find[year]', $request->find['year'] ), 
                                                            [ 'class' => 'form-control w-2' ] ) }}
                                            </div>
                                        </div>
                            </td>
                        </tr>
                        <tr>
                            <th colspan=3 class='align-left'><button class="btn btn-primary">検索</button></th>
                            <th colspan=3 align='right'><button class="btn btn-primary">検索</button></th>
                        </tr>
         
                    </table>
                    {{ Form::close() }}


                    <hr>

                    <table class='table table-bordered table-hover'>
                        <tr class='text-center thead-light'>
                            <th>アクション</th>
                            <th>部署名</th>
                            <th>役職</th>
                            <th>名前</th>
                            <th>割当年度</th>
                            <th>割当日</th>
                            <th>有効期限</th>
                            <th>割当日数</th>
                            <th>申請日数</th>
                            <th>残日数</th>
                            <th>期限切日数</th>
                        </tr>
                        @foreach( $paidleaves as $paid ) 

                            <tr class="text-center">
                                <td class="wp-auto">
                                    <a class='btn btn-sm btn-outline-primary' href='{{ route( 'vacation.vacation.show',[ 'vacation' => $paid->id ] ) }}'>詳細</a>
                                    <a class='btn btn-sm btn-outline-primary' href='{{ route( 'vacation.vacation.edit',[ 'vacation' => $paid->id ] ) }}'>変更</a>
                                </td>
                                <td>{{ ( ! empty( $paid->dept_id )) ? $depts[$paid->dept_id] : "" }}</td>
                                <td>{{ $paid->grade                           }}</td>
                                <td>{{ $paid->user_name                       }}</td>
                                <td>{{ $paid->year                            }}</td>
                                <td>{{ $paid->allocate_date                   }}</td>
                                <td>{{ $paid->expire_date                     }}</td>
                                <td>{{ Vacation::pnum( $paid->allocated_num ) }}</td>
                                <td>{{ Vacation::pnum( $paid->application_num + 
                                       $paid->approval_num + 
                                       $paid->completed_num )                 }}</td>
                                <td>{{ Vacation::pnum( $paid->remains_num )   }}</td>
                                <td>{{ Vacation::pnum( $paid->expired_num )   }}</td>
                            </tr>
                        @endforeach
                        
                         {{ Form::close() }}

                    </table>
                 {{ $paidleaves->appends( [ $request->find, 'SearchQuery' => 1] )->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@php
#dd( $request );
#dd( Session::all() );
@endphp




