@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();


$calendars = ( is_array( $request->calendars )) ? $request->calendars : [];
$tasklists = ( is_array( $request->tasklists )) ? $request->tasklists : [];

@endphp


<div class="bg-light">
    {{ Form::open( [ 'route' => $current_route, 'method' => 'GET', 'id' => 'search_form' ] ) }}
        @csrf
        {{ Form::hidden( 'base_date', $request->base_date, ['id' => 'base_date' ] ) }}

        <div class="container">
            <div class="row">
 
 
                <div class="col-12 shadow-lg p-2">
                    <div class="btn btn-outline-dark btn-light shadow col-11" onClick="search_form_submit()">再表示</div>
                </div>
                {{--
                  --
                  -- 設備リスト
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="facilities">設備</div>
                <div class="facilities" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        <x-facility_checkboxes :facilities="op( $request )->facilities" name="facilities" button="設備検索" />
                    </div>      
                </div>

                {{--
                  --
                  -- 社員・部署検索
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="users">社員・部署</div>
                <div class="users" style="width: 100%">
                    <div class="col-12 shadow border m-2 p-1">
                        <x-checkboxes_users :users="op( $request )->users" button="社員" />
                        <hr>
                        <x-checkboxes_depts :depts="op( $request )->depts" name="depts" button="部署" />
                    </div>
                </div>
                
            </div>

        </div>
        <div class="col-12 shadow-lg p-2">
            <div class="btn btn-outline-dark shadow col-11" onClick="search_form_submit()">再表示</div>
        </div>
        <script>
            function search_form_submit() {
                $("#search_form").submit();
            }
        </script>
    {{ Form::close() }}

</div>