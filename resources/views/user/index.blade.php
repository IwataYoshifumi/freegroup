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
            @include( 'user.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>


                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'user.index_find' )
                    <br>
                    @php
                        /*
                            $form['詳細'] = [ 'route_name'    => 'user.show', 
                                              'option_name'   => 'user',
                                              'option_column' => 'id', ]; 
                            $form['変更'] = [ 'route_name'    => 'user.edit', 
                                              'option_name'   => 'user',
                                              'option_column' => 'id',
                                              'class'         => 'btn-warning']; 
                        MyForm::index( [ 'rows'            => $users, 
                                            'columns'         => config( 'user.columns' ), 
                                            'columns_name'    => config( 'user.columns_name' ),
                                            'show'            => $show,
                                            'form'            => $form,
                                            ])
                        */
                    @endphp

                    <table class="table table-border">
                        <tr>
                            <th>詳細</th>
                            @if( isset( $show['dept_id'] )) <th>部署</th> @endif
                            @if( isset( $show['grade']   )) <th>役職</th> @endif
                            <th>名前</th>
                            @if( isset( $show['email']   )) <th>メール</th> @endif
                            @if( isset( $show['退職']    )) <th>退職</th>   @endif
                        </tr>
                        @foreach( $users as $user )
                            <tr>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route( 'user.show', [ 'user'=> $user->id ] ) }}">詳細</a>
                                    <a class="btn btn-sm btn-warning"      href="{{ route( 'user.edit', [ 'user'=> $user->id ] ) }}">変更</a>
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
                    
            

                    @if( isset( $find['pagination'] )) 
                        {{ $users->appends( [ 'find' => $find ] )->links() }}
                    @endif
                    <div class="w-100"></div>
                    @php
                        $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                    @endphp
        
                    {{ OutputCSV::button( [ 'route_name' => 'user.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                </div>
            </div>
        </div>
    </div>
</div>


@php
#dump( $show );
@endphp




@endsection

