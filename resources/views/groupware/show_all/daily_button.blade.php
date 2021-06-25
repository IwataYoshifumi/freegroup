@php
use Carbon\Carbon;

$this_day = $request->base_date;
$pre_day  = $base_date->copy()->subDay()->format( 'Y-m-d' );
$next_day = $base_date->copy()->addDay()->format( 'Y-m-d' );
$today    = Carbon::now()->format( 'Y-m-d' );

$url_to_create_schedule = route( 'groupware.schedule.create', [ 'start_date' => $this_day, 'end_date' => $this_day ] );
$url_to_create_task     = route( 'groupware.task.create',     [ 'due_date'   => $this_day ] );

@endphp

<div class="row">
    <a class="col-1 btn btn_icon text-primary uitooltip" title="新規スケジュール作成" href="{{ $url_to_create_schedule }}">@icon( schedule )</a>
    <a class="col-1 btn btn_icon text-primary uitooltip" title="新規タスク作成"  href="{{ $url_to_create_task     }}">@icon( check-circle )</a>
    
    <div class="col-1 w-5  btn btn_icon day_button uitooltip" title="{{ $pre_day }}へ移動" data-date="{{ $pre_day }}">@icon( angle-left )</div>
    <div class="col-3 w-15 h4 day_button font-weight-bold uitooltip"     title="今日へ移動" style="cursor: pointer;" data-date="{{ $today }}">{{ $base_date->formatLocalized( '%Y-%m-%d ( %a )' ) }} </div>
    <div class="col-1 w-5  btn btn_icon day_button uitooltip" title="{{ $next_day }}へ移動" data-date="{{ $next_day }}">@icon( angle-right )</div>
</div>
<script>
    $('.day_button').on( 'click', function() {
        var date = $(this).data('date');
        $("#base_date").val( date );
        $("#search_form").submit();
    });
</script>

{{ Form::open( [ 'route' => $route_name, 'method' => 'GET', 'id' => 'search_form' ] ) }}
    @csrf
    @foreach( $request->all() as $name => $value )
        @if( $name == "_token" ) @continue @endif
        @if( is_array( $value )) 
            @foreach( $value as $i => $v )
                {{ Form::hidden( $name."[". $i."]", $v ) }}    
            @endforeach
        @else 
            {{ Form::hidden( $name, $value, [ 'id' => $name ] ) }}
        @endif
    @endforeach
{{ Form::close() }}