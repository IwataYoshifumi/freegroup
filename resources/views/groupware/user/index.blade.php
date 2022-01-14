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

                    <div class="container-fluid">
                        <div class="row no-gutters">
                            <div class="d-none d-sm-block col-2">詳細</div>
                            @if( isset( $show['dept_id'] )) <div class="d-none d-sm-block col-2">部署</div> @endif
                            @if( isset( $show['grade']   )) <div class="d-none d-sm-block col-2">役職</div> @endif
                            <div class="d-none d-sm-block col-3">名前</div>
                            @if( isset( $show['email']   )) <div class="d-none d-sm-block col-3">メール</div> @endif
                            @if( isset( $show['retired'] )) <div class="d-none d-sm-block col-1">退職</div>   @endif
                            
                            <div class="col-12"></div>

                            @foreach( $users as $user )
                                @php
                                    $show_route = route( 'groupware.user.show', [ 'user' => $user->id ] );
                                    $edit_route = route( 'groupware.user.edit', [ 'user' => $user->id ] );
                                @endphp
                                <div class="col-12 col-md-2">
                                    <a class="btn btn-sm btn-outline-dark" href="{{ $show_route }}">詳細</a>
                                    @can( 'update', $user )
                                        <a class="btn btn-sm btn-warning"  href="{{ $edit_route }}">変更</a>
                                    @endcan
                                </div>
                                
                                @if( isset( $show['dept_id'] )) 
                                    <div class="col-5 col-md-2 text-truncate">{{ op( $user->dept )->name }}</div>
                                @endif

                                @if( isset( $show['grade'] )) 
                                    <div class="col-5 col-md-2 text-truncate">{{ $user->grade }}</div>
                                @endif
                                
                                <div class="col-12 col-md-3 text-truncate">{{ $user->name }}</div>

                                @if( isset( $show['email'] )) 
                                    <div class="col-12 col-md-3 text-truncate">{{ $user->email }}</div>
                                @endif
                                
                                @if( isset( $show['retired'] )) 
                                    <div class="col-12 col-md-1 text-truncate">
                                        @if( $user->retired ) {{ $user->date_of_retired }} 退職 @endif                                        
                                    </div>
                                @endif
                                <div class="d-none d-sm-block col-12"></div>
                                <div class="d-block d-sm-none col-12 border border-secondary mb-1 mt-1"></div>

                            @endforeach
                        </div> 
                    </div>

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