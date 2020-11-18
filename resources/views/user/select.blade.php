@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;

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

                    @include( 'user.find' )
                    <hr>
                    
                    {{ Form::open( [ 'route' => 'user.retire', 'method' => 'GET' ] ) }}
                    @csrf
                    
                    @php
                        $form['退職'] = [ 'form_name'     => 'retire', 
                                          'option_column' => 'id', ]; 
                        $form['復職'] = [ 'form_name'     => 'reinstate', 
                                          'option_column' => 'id', ]; 

                    @endphp

                    {{ MyForm::select( [ 'rows'            => $users, 
                                        //'columns'         => [ 'id', 'name', 'email', 'retired', 'date_of_retired', 'password' ], 
                                        'columns'         => config( 'user.columns' ), 
                                        'columns_name'    => config( 'user.columns_name' ),
                                        'show'            => $show,
                                        'form'            => $form,
                                       ]) }}
                                        
            
                    </table>
                    <button type='submit' class="btn btn-primary m-1">退職・復職実行</button>
                    {{ Form::close() }}
                    
                    @if( isset( $find['pagination'] )) 
                        {{ $users->appends( [ 'find' => $find ] )->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>




@php
#dump( $users );
@endphp




@endsection

