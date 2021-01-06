@php

use Carbon\Carbon;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Dept;

use App\myHttp\GroupWare\View\FindDeptsComponent;


$today = new Carbon( 'today' );

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

<div class="container border border-dark p-1 w-95 m-1 p-1">
    <div class="row w-90 container m-lg-1 p-lg-1 ">
        <div class="col-8 d-none d-lg-block p-1">期間</div>
        <div class="col-4 d-none d-lg-block p-1">表示数</div>
    </div>
    <div class="row p-1 container m-1">
        <div class="col-12 d-lg-none p-1">期間</div>
        <div class="col-lg-8 clearfix">
            <div class="row p-1">
            {{ Form::date( 'component_input_schedule_start_date', "", 
                            ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'component_input_schedule_start_date' ] ) }}
            <div class="col-1 m-1">～</div>
            {{ Form::date( 'component_input_schedule_end_date', "",  
                            ['class' => 'form-control col-8 col-lg-5 clearfix', 'id' => 'component_input_schedule_end_date' ] ) }}
            </div>
            <div class="col-lg-12">
                @foreach( $button as $key => $date ) 
                    <a class="col-3 col-lg-1 m-1 btn btn-sm btn-outline btn-outline-dark component_input_schedule_date_button" data-start='{{ $date['start'] }}' data-end='{{ $date['end'] }}'>{{ $key }}</a>
                @endforeach
            </div>
            <script>
                $('.component_input_schedule_date_button').click( function(){
                    $('#component_input_schedule_start_date').val( $(this).data('start') ); 
                    $('#component_input_schedule_end_date'  ).val( $(this).data('end') ); 
                });
            </script>  
        </div>
        
        
        <div class="col-12 d-lg-none p-1">表示数</div>
        <div class="col-lg-3 clearfix">
            {{ Form::select( 'component_input_schedule_pagination', config( 'constant.pagination' ), "" , [ 'class' => 'form-control', 'id' => 'component_input_schedule_pagination' ] ) }}
        </div>

    </div>
    <div class="row w-90 container m-lg-1 p-lg-1 ">
        <div class="col-4 d-none d-lg-block p-1">件名</div>
        <div class="col-4 d-none d-lg-block p-1">部署</div>
        <div class="col-4 d-none d-lg-block p-1">社員</div>
    </div>
    <div class="row p-1 container m-1">
        <div class="col-12 d-lg-none p-1">件名</div>
        <div class="col-lg-4 p-1 clearfix">
            {{ Form::text( 'component_input_schedule_name', "", [ 'class' => 'form-control', 'id' => 'component_input_schedule_name' ] ) }}
        </div>

        <div class="col-12 d-lg-none p-1">部署</div>
        <div class="col-lg-4 p-1 clearfix">
            @php
                $options = [ 'name' => 'component_schedule_dept_id', 'array' => Dept::getArrayforSelect(), 'class' => 'form-control', 'id' => 'component_schedule_find_dept_id' ]; 
            @endphp
            <!--- コンポーネント FindDeptComponent --->                                
            <x-find_dept :options="$options" />
                                                    
        </div>
        
        

        <div class="col-12 d-lg-none p-1">社員</div>
        <div class="col-lg-4 p-1 clearfix">
            @php
                $options = [ 'name'  => 'component_schedule_find_user_id', 
                             'class' => 'form-control', 
                             'id' => 'component_schdule_find_user_id',
                             'dept_form_id' => 'component_schedule_find_dept_id' ]; 
            @endphp
            <!--- コンポーネント FindUserComponent --->                                
            <x-find_user :options="$options" />

            
            
            
        </div>
    </div>

    <div class="col-12 container">
        <div class="row">
            <div class="btn btn-primary" id="component_input_schedule_search">検索</div>
        </div>
    </div>
    
    <div class="col-12">&nbsp;</div>
    
    <div id="component_input_schedule_lists" class="bg-light">
            <div class="col schedule"></div>
    </div>
    
    <script>
        $('#component_input_schedule_search').click( function() {
            var start_date = $('#component_input_schedule_start_date').val();
            var end_date   = $('#component_input_schedule_end_date'  ).val();
            var name       = $('#component_input_schedule_name'      ).val();
            var user_id    = $('#component_schdule_find_user_id'     ).val();
            var dept_id    = $('#component_schedule_find_dept_id'    ).val();
            var pagination = $('#component_input_schedule_pagination').val();

            var url    = "{{ route( 'groupware.schedule.json_search' ) }}";

            console.log( start_date, end_date, name, user_id, dept_id, pagination );
            
            $.ajax( url, {
                ttype: 'get',
                data:  { start_date : start_date, 
                         end_date   : end_date,
                         name       : name,
                         users      : user_id,
                         dept_id    : dept_id,
                         search_mode: 2,    /* 関連社員・スケジュール作成者を検索 */
                         pagination : pagination
                         
                },
                dataType: 'json',
            }).done( function( data ) {
                console.log( data );
                $("#component_input_schedule_lists").children().remove();
                $.each( data, function( i, val ) {
                    var tag = "<div class='col schedule schedule_ids'";
                    tag += "         id=schedule_id" + val.id;
                    tag += "         data-schedule_id=" + val.id;
                    tag += "         value=" + val.id;
                    tag += "        >";
                    tag += "    <div class='btn btn-sm btn-outline-secondary'";
                    tag += "         onClick='schedule_id_click(" + val.id + ",\"" + val.name + "\",\"" + val.p_time + "\")'";
                    tag += "    >+</div>";
                    tag += "    【" + val.user_name + "】/【" + val.p_time + "】：" + val.name;
                    tag += "    &nbsp;<a href='"+ val.url + "' target='_blank'><span class='search icon'></span></a>";
                    tag += "</div>";

                    // console.log( tag, name );
                    $("#component_input_schedule_lists").append( tag ); 
                });
            });  
        });
        
    //　ファイルID追加
    //
    function schedule_id_click( id, name, time ) {
        console.log( 'aa', id, name, time );
        try {
            $('.schedule_id').each( function() {
                console.log( $(this).data('schedule_id') ); 
                if( id === $(this).data('schedule_id') ) {
                    // console.log( 'duplicate');
                    throw new Error('duplicate id');
                }
            });
            
            var form = $('#schedule_ids_form');
            /*
            var tag = "<div class='col schedule_id' id='schedule_id_" + id + "' data-schedule_id=" + id +">";
            tag    += "     <div class='btn btn-sm btn-outline-secondary'";
            tag    += "          onClick='delete_schedule_id(" + id + ")'>-</div>";
            tag    += "     <input type=hidden name='attached_schedules[]' value=" + id +">【" + time + "】：" + name;
            tag    += "</div>";
            form.append( tag );
            */
            
            var tag = "<div class='col-12'>";
            tag    += "<div class='detach_schedule btn btn-sm btn-outline-secondary'>-</div>";
            tag    += "<input type='hidden' name='schedules[]' value='" + id + "'>";
            tag    += "<pre class='text-trancated'>【" + time +"】【" + name + "】：</pre>";
            tag    += "</div>"
            form.append( tag );
            
            // console.log( id );
        } catch( e ) {
            console.log( 'schedule_id_click duplicate ID');
        }
    };
        
        
        
    </script>
    


</div>