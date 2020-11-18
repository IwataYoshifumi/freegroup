@php

use Carbon\Carbon;

use App\Models\Dept;
use App\myHttp\Models\User;
use App\myHttp\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;

$today = new Carbon( 'today' );

Carbon::setWeekStartsAt(Carbon::SUNDAY); // 週の最初を日曜日に設定
Carbon::setWeekEndsAt(Carbon::SATURDAY); // 週の最後を土曜日に設定

$button['今年']['start'] = Carbon::parse( 'first day of January' )->format('Y-m-d');
$button['今年']['end']   = Carbon::parse( 'last day of December'  )->format('Y-m-d'); 

$button['先月']['start'] = Carbon::parse( 'first day of last month' )->format('Y-m-d');
$button['先月']['end']   = Carbon::parse( 'last day of last month'  )->format('Y-m-d');

$button['今月']['start'] = Carbon::parse( 'first day of this month' )->format('Y-m-d');
$button['今月']['end']   = Carbon::parse( 'last day of this month' )->format('Y-m-d');                                    

$button['先週']['start'] = $today->copy()->startOfWeek()->subDays(7)->format('Y-m-d');
$button['先週']['end']   = $today->copy()->endOfWeek()->subDays(7)->format('Y-m-d');                         

$button['今週']['start'] = $today->copy()->startOfWeek()->format('Y-m-d');
$button['今週']['end']   = $today->copy()->endOfWeek()->format('Y-m-d');

$button['今日']['start'] = $today->format('Y-m-d');
$button['今日']['end']   = $today->format('Y-m-d'); 

@endphp

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index.form' ] ) }}
    {{ Form::hidden( 'SearchQuery', 1 ) }}
    @csrf

    <div class="container border border-dark p-1 w-95 m-1 p-1">
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-4 d-none d-lg-block p-1">期間</div>
        </div>
        <div class="row p-1 container m-1">
            <div class="col-12 d-lg-none p-1">期間</div>
            <div class="col-lg-12 clearfix">
                <div class="row p-1">
                {{ Form::date( 'find[start_date]', old( 'find[start_date]', optional($find)['start_date'] ), 
                                ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'start_date' ] ) }}
                <div class="col-1 m-1">～</div>
                {{ Form::date( 'find[end_date]', old( 'find[end_date]', optional($find)['end_date'] ), 
                                ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'end_date' ] ) }}
                </div>
                <div class="col-lg-12">
                    @foreach( $button as $key => $date ) 
                        <a class="col-3 col-lg-1 m-1 btn btn-sm btn-outline btn-outline-dark date_button" data-start='{{ $date['start'] }}' data-end='{{ $date['end'] }}'>{{ $key }}</a>
                    @endforeach
                </div>
                <script>
                    $('.date_button').click( function(){
                        $('#start_date').val( $(this).data('start') ); 
                        $('#end_date').val( $(this).data('end') ); 
                    });
                </script>  
            </div>
        </div>
        <div class="row w-90 container m-lg-1 p-lg-1 ">
            <div class="col-4 d-none d-lg-block p-1">部署</div>
            <div class="col-4 d-none d-lg-block p-1">社員</div>
            <div class="col-4 d-none d-lg-block p-1">顧客</div>
        </div>
        <div class="row p-1 container m-1">
            
            <div class="col-12 d-lg-none p-1">部署</div>
            <div class="col-lg-4 p-1 clearfix">
                @php 
                    $depts = Dept::getArrayforSelect();
                @endphp
                {{ Form::select( 'find[dept_id]', $depts, optional($find)['dept_id'], [ 'class' => 'form-control' ] ) }} 
            </div>

            <div class="col-12 d-lg-none p-1">社員</div>
            <div class="col-lg-4 p-1 clearfix">
                @php

                    $users = ( isset( optional($find)['users'] )) ? $find['users'] : [];
                    # $users = ( is_array( $request->users )) ? $request->users : $defaults_users ;
                    #dump( $users );
                @endphp
                
                <x-input_users :users="$users"/>
                <div class="col-12">検索条件</div>
                <div class="col-12">
                      {{ Form::select( 'search_mode', Schedule::get_array_for_search_mode(), $request->search_mode, [ 'class' => 'form-control' ] ) }}
                </div>
            </div>

            <div class="col-12 d-lg-none p-1">顧客</div>
            <div class="col-lg-4 p-1 clearfix">
                @php
                    $customers = ( isset( optional($find)['customers'] )) ? $find['customers'] : [];
                    
                @endphp
                
                <x-input_customers :customers="$customers"/>
            </div>





        </div>



    

        <div class="col-12 container">
            <div class="row">
                <button class="col-3 col-lg-2 btn btn-search m-1 p-1">検索</button>
            </div>
        </div>
    
    
    </div>
{{ Form::close() }}