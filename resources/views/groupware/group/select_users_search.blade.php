@php

use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;

#dump( request()->input() );

$lists = ( is_array( optional( $find )['list'] )) ? $find['list'] : [];

$array_role_groups = toArray( RoleGroup::all() );
$array_role_groups[-1] = '【ロール未割当者】';

$array_depts = toArray( Dept::all );

@endphp

{{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET' ] ) }}
    @csrf
    <div class="border border-dark m-1">
        <div class="row m-2">
            {{ Form::text( 'find[name]', old('find[name]', optional( $find )['name'] ), [ 'class' => 'form-control col-5 m-2', 'placeholder' => '社員名'  ] ) }}
            {{ Form::select( 'find[dept_id]', $array_depts , optional($find)['dept_id'], 
                    [ 'class' => 'form-control col-3 m-2', 'placeholder' => '部署' ] ) }}
            {{ Form::select( 'find[pagination]', [ 10 => 10, 20 => 20, 30 => 30 ], old( 'find[pagination]', optional( $find )['pagination'] ), 
                    [ 'class' => 'form-control col-2 m-2' ] ) }}
            <div class="col-12"></div>
            
            {{ Form::select( 'find[role_group_id]', $array_role_groups, old( 'find[role_group_id]', optional($find)['role_group_id'] ), 
                    [ 'class' => 'form-control col-5 m-2', 'placeholder' => '割当ロール']) }}

            <div class="col-12"></div>
            <div>
                <button type='submit' class="btn btn-search m-2">検索</button>
            </div>    
            
        </div>
    </div>
{{ Form::close() }}
