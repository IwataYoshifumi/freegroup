
<div class="row">
    <label for="name" class="col-md-4 col-form-label text-md-right d-none d-lg-block">{{ config( 'customer.columns_name' )['name'] }}</label>
    <div class="col-md-6" id="toggle_customer_detail_btn"><span class="btn btn-white">{{ $customer->name }}</span>
        <span class="btn btn_icon">@icon( caret-down )</span>
    </div>
</div>

<script>
    $(document).ready( function() {
        $('#customer_detail_part').toggle();
    });
    $("#toggle_customer_detail_btn").on( 'click', function() {
        console.log( 'aaa' );
        $('#customer_detail_part').toggle( 'blind', 100 );
    });
    
    
</script>

<div id="customer_detail_part">

    <div class="row">
        <label for="email" class="col-md-4 col-form-label text-md-right d-none d-lg-block">{{ config( 'customer.columns_name' )['kana'] }}</label>
        <div class="col-md-6">{{ $customer->kana }}</div>

        <label for="email" class="col-md-4 col-form-label text-md-right d-none d-lg-block">{{ config( 'customer.columns_name' )['email'] }}</label>
        <div class="col-md-6">{{ $customer->email }}</div>

        <label for="email" class="col-md-4 col-form-label text-md-right d-none d-lg-block">住所</label>
        <div class="col-md-6">{{ $customer->p_address() }}</div>

        <label for="tel" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['tel'] }}</label>
        <div class="col-md-6">{{ $customer->tel }}</div>

        <label for="fax" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['fax'] }}</label>
        <div class="col-md-6">{{ $customer->fax }}</div>

        <label for="moblie" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['mobile'] }}</label>
        <div class="col-md-6">{{ $customer->mobile }}</div>

        @if( $customer->birth_day ) 
            <label for="birth_day" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['birth_day'] }}</label>
            <div class="col-md-6">{{ $customer->birth_day }}  {{ $customer->p_age() }}</div>
        @endif

        @if( $customer->sex )
            <label for="sex" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['sex'] }}</label>
            <div class="col-md-6">{{ $customer->sex }}</div>
        @endif

        @if( 0 && config( 'customer.salseforce.enable' ))
            <div class="row">
                <label for="sex" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['salseforce_id'] }}</label>
                <div class="col-md-6">
                    @if( ! is_null( $customer->salseforce_id )) 
                        <a class="btn btn-sm btn-outline-secondary" href="{{ config('customer.salseforce.url' ) }}/{{ $customer->salseforce_id }}" target="_blank">セールスフォースへ</a>
                    @else 
                        未登録
                    @endif
                </div>
            </div>
        @endif
    
        @if( $customer->memo )
            <label for="memo" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['memo'] }}</label>
            <div class="col-md-6">{{ $customer->memo }}</div>
        @endif

    </div>
</div>
