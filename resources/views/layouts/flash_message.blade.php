@if (Session::has('info_message'))
    <div class="alert alert-warning h5 flush">{!! Session::get('info_message') !!}</div>
@endif

@if( session('prepare_validation_message'))
    <div class="alert alert-warning m-2 p-2">
    {{ session('prepare_validation_message') }}
    </div>
@endif 

@if( session('error_message'))
    <div class="alert-danger m-2 p-2">
    {{ session('error_message') }}
    </div>
@endif

@if( session('flash_message'))
    <div class="alert-info m-2 p-2">
    {{ session('flash_message') }}
    </div>
@endif


