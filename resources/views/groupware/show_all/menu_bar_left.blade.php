@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();

$calendars = ( is_array( $request->calendars )) ? $request->calendars : [];
$tasklists = ( is_array( $request->tasklists )) ? $request->tasklists : [];

@endphp


<div class="left_area border border-light shadow " id="left_area">
    {{ Form::open( [ 'route' => $current_route, 'method' => 'GET', 'id' => 'search_form' ] ) }}
        @csrf
        {{ Form::hidden( 'base_date', $request->base_date, ['id' => 'base_date' ] ) }}

        <div class="container">
            <div class="row">
                <div class="col-12 d-flex sidebar_headar border border-dark" style="background-color: peachpuff">
                    <span class="btn btn_icon m-1 mr-auto" id="sidebar_closer">@icon( arrow-left ) </span>
                </div>
 
 
                <div class="col-12 shadow-lg p-2">
                    <div class="btn btn-outline-dark btn-light shadow col-11" onClick="search_form_submit()">再表示</div>
                </div>
                {{--
                  --
                  -- カレンダー　表示フォーム
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="calendars">カレンダー</div>
                <div class="calendars" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        <x-calendar_checkboxes :calendars="op( $request )->calendars" name="calendars" button="カレンダー検索" />
                    </div>      
                </div>

                {{--
                  --
                  -- タスクリスト　表示フォーム
                  --
                  --
                  --}}
                @php
                $array_task_status = [ '完了' => '完了', '未完' => '未完のみ', '' => '完了・未完' ];                
                @endphp
                <div class="col-12 shadow btn btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="tasklists">タスクリスト</div>
                <div class="tasklists" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        {{ Form::select( 'task_status', $array_task_status, $request->task_status, [ 'class' => 'formcontrol' ] ) }}
                        <x-tasklist_checkboxes :tasklists="op( $request )->tasklists" name="tasklists" button="タスクリスト検索" />
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
                        <hr>
                        <label for="search_users">関連社員でも検索</label>
                        {{ Form::checkbox( 'search_users', 1, $request->search_users, [ 'id' => 'search_users', 'class' => 'checkboxradio' ] ) }}
                    </div>
                </div>
                
                {{--
                  --
                  -- 顧客検索
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="customers">顧客</div>
                <div class="customers" style="width: 100%">
                    <div class="col-12 shadow border m-2 p-1">
                        <x-checkboxes_customers :customers="op( $request )->customers" name="customers" button="顧客" />
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

<script>
    $(window).on( 'load', function() {
        console.log( 'load' );
        @if(( ! is_array( $request->calendars ) or count( $request->calendars ) == 0 )) 
            $(".calendars").toggle();
        @endif
        
        @if(( ! is_array( $request->tasklists ) or count( $request->tasklists ) == 0 )) 
            $(".tasklists").toggle();
        @endif
        
        @if(( ! is_array( $request->users ) or count( $request->users ) == 0 ) and ( ! is_array( $request->depts ) or count( $request->depts ) == 0 )) 
            $(".users").toggle();
        @endif
        
        @if( ! is_array( $request->customers ) or count( $request->customers ) == 0 ) 
            $(".customers").toggle();
        @endif
    });
</script>
