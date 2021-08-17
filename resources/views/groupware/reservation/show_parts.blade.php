@php
use App\myHttp\GroupWare\Models\User;

$route_edit   = route( 'groupware.reservation.update', [ 'reservation' => $reservation->id ] );
$route_delete = route( 'groupware.reservation.delete', [ 'reservation' => $reservation->id ] );

$route_name = Route::currentRouteName();

$target = ( $route_name == 'groupware.reservation.show_modal' ) ? "target='_parent'" : ""; 

@endphp

<div class="container">
    
    @if( $route_name == "groupware.reservation.show" or $route_name == "groupware.reservation.show_modal" )
        @can( 'update', $reservation )
            <a class="btn col-1 m-1 uitooltip" style="font-size: 20px;" href="{{ $route_edit }}" title="変更"  {!! $target !!}>   {{-- htmlspecialchars OK --}}
                <i class="fas fa-pen"></i>
            </a>
        @endcan
            
        @can( 'delete', $reservation )
            <a class="btn col-1 m-1 uitooltip text-danger" style="font-size: 20px;" href="{{ $route_delete }}" title="予約キャンセル"  {!! $target !!}>   {{-- htmlspecialchars OK --}}
                <i class="fas fa-trash-alt"></i>
            </a>
        @endcan
    @endif
    
    
    <div class="row">
        @if( is_debug()) <div class="col-12">Reservation ID : {{ $reservation->id }} </div>@endif
    
        <label for="name" class="col-4 col-form-label text-md-right">予約目的</label>
        <div class="col-8">
            {{ $reservation->purpose }}
        </div>
    
        <label for="place" class="col-4 col-form-label text-md-right">予約者</label>
        <div class="col-8">
            {{ $reservation->user->dept->name }} {{ $reservation->user->name }}
        </div>
    
        <label for="place" class="col-4 col-form-label text-md-right">設備</label>
        <div class="col-8">
            {{ op( $reservation->facility )->name }}

            @if( $facility->is_disabled() )
                <span class="uitooltip" title="設備管理者による制限によって、無効化されました。">
                    <i class="fas fa-info-circle"></i>
                </span>
            @endif
        </div>
    
        <label for="email" class="col-4 col-form-label text-md-right">予約期間</label>
        <div class="col-8">
            {{ $reservation->p_time('index') }}
        </div>
    
        {{--
        @if( count( $users ))
            <label for="customers" class="col-4 col-form-label text-md-right">関連社員</label>
            <div class="col-8">
                @foreach( $users as $u )
                    <div class="col-12">
                        【 {{ $u->dept->name }} 】{{ $u->name }} {{ $u->grade }}
                    </div>        
                @endforeach
            </div>
        @endif
        --}}
    
        <label for="mobile" class="col-4 col-form-label text-md-right">備考</label>
        <div class="col-8">
            <pre>{{ $reservation->memo }}</pre>
        </div>
        
    </div>
</div>