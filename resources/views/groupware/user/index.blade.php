@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\User;

#dump( Request::all() );
#dump( session( 'back_button' ) );
@endphp


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'groupware.user.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>


                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'groupware.user.index_find' )
                    <br>

                    <table class="table table-border table-sm">
                        <tr>
                            <th>詳細</th>
                            @if( isset( $show['dept_id'] )) <th>部署</th> @endif
                            @if( isset( $show['grade']   )) <th>役職</th> @endif
                            <th>名前</th>
                            @if( isset( $show['email']   )) <th>メール</th> @endif
                            @if( isset( $show['retired'] )) <th>退職</th>   @endif
                        </tr>
                        @foreach( $users as $user )
                            @php
                                $show_route = route( 'groupware.user.show', [ 'user' => $user->id ] );
                                $edit_route = route( 'groupware.user.edit', [ 'user' => $user->id ] );
                            @endphp
                            <tr>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="{{ $show_route }}">詳細</a>
                                    @can( 'update', $user )
                                        <a class="btn btn-sm btn-warning"  href="{{ $edit_route }}">変更</a>
                                    @endcan
                                </td>
                                
                                @if( isset( $show['dept_id'] )) 
                                    <td>{{ $user->dept->name }}</td>
                                @endif

                                @if( isset( $show['grade'] )) 
                                    <td>{{ $user->grade}}</td>
                                @endif
                                
                                <td>{{ $user->name }}</td>

                                @if( isset( $show['email'] )) 
                                    <td>{{ $user->email }}</td>
                                @endif
                                
                                @if( isset( $show['retired'] )) 
                                    <td>
                                        @if( $user->retired ) {{ $user->date_of_retired }} 退職 @endif                                        
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>

                    @if( method_exists( $users, 'links' )) 
                        {{ $users->appends( request()->all() )->links() }}
                    @endif
                    <div class="w-100"></div>
                    @php
                        $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                    @endphp
        
                </div>
            </div>
        </div>
    </div>
</div>




@endsection