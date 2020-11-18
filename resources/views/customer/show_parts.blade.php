
                <div class="row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['name'] }}</label>
                    <div class="col-md-6">{{ $customer->name }}</div>
                </div>
                <div class="row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['kana'] }}</label>
                    <div class="col-md-6">{{ $customer->kana }}</div>
                </div>

                <div class="row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['email'] }}</label>
                    <div class="col-md-6">{{ $customer->email }}</div>
                </div>

                <div class="row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">住所</label>
                    
                    <div class="col-md-6">{{ $customer->p_address() }}</div>
                </div>

                <div class="row">
                    <label for="tel" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['tel'] }}</label>
                    <div class="col-md-6">{{ $customer->tel }}</div>
                </div>

                <div class="row">
                    <label for="fax" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['fax'] }}</label>
                    <div class="col-md-6">{{ $customer->fax }}</div>
                </div>

                <div class="row">
                    <label for="moblie" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['mobile'] }}</label>
                    <div class="col-md-6">{{ $customer->mobile }}</div>
                </div>

                <div class="row">
                    <label for="birth_day" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['birth_day'] }}</label>
                    <div class="col-md-6">{{ $customer->birth_day }}  {{ $customer->p_age() }}</div>
                </div>
                
                <div class="row">
                    <label for="sex" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['sex'] }}</label>
                    <div class="col-md-6">{{ $customer->sex }}</div>
                </div>
                
                @if( config( 'customer.salseforce.enable' ))
                    <div class="row">
                        <label for="sex" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['salseforce_id'] }}</label>
                        <div class="col-md-6">
                            @if( ! is_null( $customer->salseforce_id )) 
                                <a class="btn btn-outline-secondary" href="{{ config('customer.salseforce.url' ) }}/{{ $customer->salseforce_id }}" target="_blank">セールスフォースへ</a>
                            @else 
                            
                            @endif
                        </div>
                    </div>
                @endif
                
                
                <div class="row">
                    <label for="memo" class="col-md-4 col-form-label text-md-right">{{ config( 'customer.columns_name' )['memo'] }}</label>
                    <div class="col-md-6">{{ $customer->memo }}</div>
                </div>

