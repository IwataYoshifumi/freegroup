@extends('layouts.app')

@php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Admin;

#dump( Request::all() );
#dump( session( 'back_button' ) );
@endphp


@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include( 'admin.menu_button' )

            <div class="card">
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">
                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )

                    @include( 'admin.index_find' )
                    <hr>
                    
                    @php
                        $form['詳細'] = [ 'route_name'    => 'admin.show', 
                                          'option_name'   => 'admin',
                                          'option_column' => 'id', ]; 
                        $form['変更'] = [ 'route_name'    => 'admin.edit', 
                                          'option_name'   => 'admin',
                                          'option_column' => 'id',
                                          'class'         => 'btn-warning']; 
                    @endphp

                    {{ MyForm::index( [ 'rows'            => $admins, 
                                        'columns'         => config( 'admin.columns' ), 
                                        'columns_name'    => config( 'admin.columns_name' ),
                                        'show'            => $show,
                                        'form'            => $form,
                                       ]) }}
                                        
            
                    </table>
                    @if( isset( $find['pagination'] )) 
                        {{ $admins->appends( [ 'find' => $find ] )->links() }}
                    @endif
                    <div class="w-100"></div>
                    @php
                        $inputs = [ 'find' => $find, 'show' => [ 'array' => $show, $find ]];
                    @endphp
        
                    {{ OutputCSV::button( [ 'route_name' => 'admin.csv', 'inputs' => $inputs , 'method' => 'GET' ]) }}
                </div>
            </div>
        </div>
    </div>
</div>




@php

foreach( $admins as $admin ) {
#     if( $admin->is_admin() ) { print "admin"; } 
 #   if( ! $admin->is_user() ) { print "not user"; }
    $admin->is_locked();
}
@endphp




@endsection

