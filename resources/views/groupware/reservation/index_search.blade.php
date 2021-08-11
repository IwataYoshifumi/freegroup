@php

use App\Models\Dept;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Facility;

use App\Http\Helpers\BackButton;


@endphp

{{ Form::open( [ 'route' => 'groupware.reservation.index' , 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    <div class="border border-dark m-1">
        <div class="row">        
            <div class="col-3 ">
                <x-facility_checkboxes :facilities="op( $request )->facilities" name="facilities" button="設備検索" />
            </div>
            <div class="col-8 p-1">
                <div class='row'>
                    <div class="col-11 m-1 p-1 border border-dark">
                        <div class="m-2">予約期間</div>
                        @php
                        $start = "start_date";
                        $end   = "end_date";
                        @endphp
                        <x-input_date_span :start="$start" :end="$end" />
                    </div>
                </div>
            </div>
            <div class="col-11 container-fluid">
                <div class="row no-gutters">
                    <div class="col-5 m-1 border border-dark p-1">
                        <x-checkboxes_users :users="op( $request )->users" button="社員" />
                    </div>
                    <div class="col-5 m-1 border border-dark p-1">
                        <x-checkboxes_depts :depts="op( $request )->depts" name="depts" button="部署" />                    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 m-2">
            <a class="btn btn-secondary text-white m-1" onClick="submit_btn()">検索</a>
        </div>
    </div>
{{ Form::close() }}
<script>
    function submit_btn() {
        console.log( 'aaa' );
        $('#search_form').submit();
    }
</script>

<hr>

