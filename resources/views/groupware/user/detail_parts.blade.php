
@if( auth( 'admin' )->check() )

    <div class="d-none d-lg-block">
        <div class="row">
            <div class="col-md-3">{{ $user->dept->name }}</div>
            <div class="col-md-4">{{ $user->name }}</div>
            <div class="col-md-4">{{ $user->email }}</div>
        </div>
    </div>
    
    <div class="d-block d-lg-none">
        <div class="row">
            <div class="col-11">{{ $user->dept->name }}</div>
            <div class="col-11">{{ $user->name }}</div>
        </div>
    </div>
    
    <div class="row">
        <label for="memo" class="col-md-4 col-form-label text-md-right">{{ config( 'user.columns_name' )['memo'] }}</label>
        <div class="col-md-6">{{ $user->memo }}</div>
    </div>
@endif

