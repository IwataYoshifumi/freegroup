@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Admin Home</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @include( 'layouts.error' )
                    @include( 'layouts.flash_message' )

                    Admin are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
