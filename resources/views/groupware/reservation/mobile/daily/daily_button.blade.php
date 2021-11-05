@php
use Carbon\Carbon;

$base_date = new Carbon( $request->base_date );

$this_day = $request->base_date;
$pre_day  = $base_date->copy()->subDay()->format( 'Y-m-d' );
$next_day = $base_date->copy()->addDay()->format( 'Y-m-d' );
$today    = Carbon::now()->format( 'Y-m-d' );

$url_to_create_schedule = route( 'groupware.schedule.create', [ 'start_date' => $this_day, 'end_date' => $this_day ] );
$url_to_create_task     = route( 'groupware.task.create',     [ 'due_date'   => $this_day ] );

$url_to_monthly = route( 'groupware.show_all.monthly' );
$url_to_weekly  = route( 'groupware.show_all.weekly'  );


@endphp

<div class="d-flex justify-content-center" style="font-size: normal;">
    <div class="btn day_button" data-date="{{ $pre_day  }}">@icon( angle-left )</div>
    <div class="btn day_button" data-date="{{ $today    }}">{{ $base_date->formatLocalized( '%Y-%m-%d ( %a )' ) }} </div>
    <div class="btn day_button" data-date="{{ $next_day }}">@icon( angle-right )</div>
</div>
<script>
    //　日付切替ボタン
    //
    $('.day_button').on( 'click', function() {
        var date = $(this).data('date');
        $("#base_date").val( date );
        $("#search_form").submit();
    });
    
    //　月表示・週表示　切替ボタン
    //
    $('.switch_form_btn').on( 'click', function() {
        var url = $(this).data('url');
        $('#search_form').attr('action', url );
        $('#search_form').submit();
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