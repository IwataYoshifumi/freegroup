@php

use Illuminate\Support\Facades\Route;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;

use App\Http\Helpers\BackButton;

#dump( request()->input() );

$lists = ( is_array( optional( $find )['list'] )) ? $find['list'] : []; 

@endphp

{{ Form::open( [ 'route' => Route::currentRouteName(), 'method' => 'GET' ] ) }}
    @csrf
    <div class="border border-dark m-1">
        <div>
            {{ Form::text( 'find[name]', old('find[name]', optional( $find )['name'] ), [ 'class' => 'form-control col-5 m-2', 'placeholder' => 'ロールグループ名'  ] ) }}

        </div>
        <div class="btn m-2"
            data-toggle="collapse"
            data-target="#role_list"
            aria-expand="false"
            aria-controls="role_list"
            >
            ロール検索
        </div>
        
        <div class="m-2 collapse" id="role_list">
            @foreach( RoleList::get_array_role_lists() as $role => $memo ) 
                <div>
                    
                    {{ Form::checkbox( 'find[list][]', $role, in_array( $role, $lists )) }}
                    {{ $memo }}<br>

                </div>
                
            
            @endforeach
        </div>
            
            
            
        <div class="col-12"></div>
        <div>
            <button type='submit' class="btn btn-search m-2">検索</button>
            
        </div>    
            
        </div>
    </div>
{{ Form::close() }}
