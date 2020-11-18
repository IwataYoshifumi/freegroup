@if( $errors->count() > 0 ) 
    <div class="m-2">
    @foreach( $errors->all() as $key => $error ) 
        <div class="text-danger">{{ $error }}</div>
    @endforeach
    </div>
@endif