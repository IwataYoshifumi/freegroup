@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer;

#dump( Request::all() );
#dump( session( 'back_button' ) );

@endphp


@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'customer.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'customer.index_find' )
                    <hr>
                    
                    @php
                        $form['表示'] = [ 'route_name'    => 'customer.show', 
                                          'option_name'   => 'customer',
                                          'option_column' => 'id', ]; 
                        $form['変更'] = [ 'route_name'    => 'customer.edit', 
                                          'option_name'   => 'customer',
                                          'option_column' => 'id',
                                          'class'         => 'btn-warning']; 
                        /*
                        $form['削除'] = [ 'route_name'    => 'customer.delete', 
                                          'option_name'   => 'customer',
                                          'option_column' => 'id',
                                          'class'         => 'btn-danger']; 
                        */
                                          
                    @endphp

                    {{ MyForm::index( [ 'rows'            => $customers, 
                                        'columns'         => config( 'customer.columns' ), 
                                        'columns_name'    => config( 'customer.columns_name' ),
                                        'show'            => $show,
                                        'form'            => $form,
                                       ]) }}
                                        
            
                    </table>
                    <div class="col-12 m-1">
                        @if( isset( $find['paginate'] )) 
                            {{ $customers->appends( [ 'find' => $find, 'show' => $show, 'sort' => $sort ] )->links() }}
                        @endif
                    </div>
                    <div class="w-100"></div>
                    @php
                        $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                    @endphp
        
                    {{ OutputCSV::button( [ 'route_name' => 'customer.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                </div>
            </div>
        </div>
    </div>
</div>




@php


@endphp




@endsection

