<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection ;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\ScreenSize;

use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;

use App\myHttp\GroupWare\Models\Actions\ReservationAction;

use App\myHttp\GroupWare\Requests\StoreReservationRequest;
use App\myHttp\GroupWare\Requests\ReservationRequest;

use App\myHttp\GroupWare\Controllers\Search\SearchReservation;
use App\myHttp\GroupWare\Controllers\Search\SearchReservationMonthly;
use App\myHttp\GroupWare\Controllers\Search\SearchReservationWeekly;
use App\myHttp\GroupWare\Controllers\Search\SearchReservationDaily;
use App\myHttp\GroupWare\Controllers\Search\IndexReservation;
use App\myHttp\GroupWare\Controllers\Search\CheckAvailableFacilities;

use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;
use App\myHttp\GroupWare\Controllers\SubClass\DateTimeInput;

class ReservationController extends Controller {
    
    
    public function index( Request $request ) {
        
        if( $request->from_menu ) {
            $request->facilities = Facility::whereCanWrite( user_id() )->where('disabled', 0)->pluck('id')->toArray();
        }
        $reservations = IndexReservation::search( $request );
        
        BackButton::setHere( $request );
        return view( 'groupware.reservation.index' )->with( 'request', $request )
                                                    ->with( 'reservations', $reservations );
    }
    
    //  設備予約状況（月表示）
    //
    public function monthly( Request $request ) {
        
        if( ! isset( $request->base_date  )) { $request->base_date  = Carbon::today()->format( 'Y-m-d' ); }
        if( ! isset( $request->facilities )) { $request->facilities = Facility::whereCanWrite( user_id() )->where('disabled', 0 )->pluck('id')->toArray(); }
        
        $returns = SearchReservationMonthly::search( $request );
        
        BackButton::setHere( $request );
        if( ScreenSize::isMobile() ) {
            return view( 'groupware.reservation.mobile.monthly' )->with( 'request', $request )
                                                                 ->with( 'returns', $returns );
                
        } else {
            return view( 'groupware.reservation.monthly' )->with( 'request', $request )
                                                          ->with( 'returns', $returns );
        }
    }

    //  設備予約状況（週表示）
    //
    public function weekly( Request $request ) {
        
        if( ! isset( $request->base_date  )) { $request->base_date  = Carbon::today()->format( 'Y-m-d' ); }
        if( ! isset( $request->facilities )) { $request->facilities = Facility::whereCanWrite( user_id() )->where('disabled', 0 )->pluck('id')->toArray(); }
        
        $returns = SearchReservationWeekly::search( $request );
        
        BackButton::stackHere( $request );
        return view( 'groupware.reservation.weekly' )->with( 'request', $request )
                                                     ->with( 'returns', $returns );
    }
    
    //　設備予約状況（日次表示、モバイル用）
    //
    public function daily( Request $request ) {

        if( ! isset( $request->base_date  )) { $request->base_date  = Carbon::today()->format( 'Y-m-d' ); }
        if( ! isset( $request->facilities )) { $request->facilities = Facility::whereCanWrite( user_id() )->where('disabled', 0 )->pluck('id')->toArray(); }
    
        $returns = SearchReservationDaily::search( $request );
    
        // dd( __METHOD__, $returns, $request );
        
        BackButton::stackHere( $request );
        return view( 'groupware.reservation.mobile.daily.daily' )->with( 'request', $request )
                                                                 ->with( 'returns', $returns );
        
    }
    
    // 　ルーティングコントローラー
    //
    public function create( Request $request ) {
        $this->authorize( 'create', Reservation::class );

        if( is_null( $request->start ) or is_null( $request->end )) { 

            // 新規作成
            //
            $reservation = new Reservation;

            $input    = new DateTimeInput();
            if( isset( $request->start_date ) and isset( $request->end_date )) {
                $input->start_date = $request->start_date;
                $input->end_date   = $request->end_date;
            }
        }

        // return view( 'groupware.reservation.create' )->with( 'defaults', $defaults );
        BackButton::stackHere( $request );
        return view( 'groupware.reservation.input' )->with( 'reservation', $reservation )
                                                    ->with( 'request', $request )
                                                    ->with( 'input',    $input    );
    }
    
    //　月表示・週表示画面で設備予約（モーダルウインドウ）
    //
    public function createModal( Request $request ) {
        $this->authorize( 'create', Reservation::class );

        // dd( $request->all() );

        $reservation = new Reservation;
        $input    = new DateTimeInput();
        
        if( $request->base_date ) {
            $input->start_date = $request->base_date;            
            $input->end_date   = $request->base_date;
        } 

        // return view( 'groupware.reservation.create' )->with( 'defaults', $defaults );
        BackButton::stackHere( $request );
        return view( 'groupware.reservation.input_modal' )->with( 'reservation', $reservation )
                                                          ->with( 'request', $request )
                                                          ->with( 'input',    $input    );
    }
    
    public function store( StoreReservationRequest $request ) {

        $this->authorize( 'create', Reservation::class );
        
        $reservations = ReservationAction::creates( $request );
        
        // if_debug( 'request->all', $request,  $reservation );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "設備を予約が完了しました。" );

        //　予約一覧へリダイレクト
        //
        $string  = "start_date=$request->start_date";
        $string .= "&end_date=$request->end_date";
        foreach( $reservations as $i => $reservation ) {
            $string .= "&facilities[$i]=$reservation->facility_id";
        }
        $url = route( 'groupware.reservation.index' ) . "?$string";        
        return redirect( $url );
    }

    //　設備予約（モーダルウインドウ）
    //
    public function storeModal( StoreReservationRequest $request ) {

        $this->authorize( 'create', Reservation::class );
        
        $reservations = ReservationAction::creates( $request );

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "設備を予約が完了しました。" );

        //　結果表示へリダイレクト
        //
        $string  = "";
        $j = 0;
        foreach( $reservations as $i => $reservation ) {
            if( $j == 0 ) {
                $string .= "&";
                $j++;
            }
            $string .= "reservations[$i]=$reservation->id";
        }
        $url = route( 'groupware.reservation.stored_modal' ) . "?$string";        
        return redirect( $url );
    }

    //　設備予約　完了　結果表示（モーダルウインドウ）
    //
    public function storedModal( Request $request ) {
        if( empty( $request->reservations ) or count( $request->reservations ) == 0 ) { die(); }
        $reservations = Reservation::whereIn( 'id', $request->reservations )->with( 'facility' )->get();
        
        foreach( $reservations as $reservation ) { $this->authorize( 'view', $reservation ); }

        // if_debug( $request->all(), $reservations );

        return view( 'groupware.reservation.stored_modal' )->with( 'reservations', $reservations )
                                                           ->with( 'request', $request );        
            
        
    }
    
    public function edit( Reservation $reservation, Request $request ) {
        
        $this->authorize( 'update', $reservation );
        
        BackButton::stackHere( request() );
        return view( 'groupware.reservation.input' )->with( 'reservation', $reservation )
                                                    ->with( 'request',     $request     );
    }
    
    public function update( Reservation $reservation, ReservationRequest $request ) {
        
        $this->authorize( 'update', $reservation );

        $reservation = ReservationAction::updates( $reservation, $request );        

        // session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "設備予約を修正しました。" );
        
        return redirect()->route( 'groupware.reservation.show', [ 'reservation' =>  $reservation->id ]);
    }
    
    public function show( Reservation $reservation ) {

        $this->authorize( 'view', $reservation );
        
        BackButton::stackHere( request() );
        return view( 'groupware.reservation.show' )->with( 'reservation', $reservation );
    }

    public function showModal( Reservation $reservation ) {
        $this->authorize( 'view', $reservation );
        
        return view( 'groupware.reservation.show_modal' )->with( 'reservation', $reservation );
    }
    
    public function delete( Reservation $reservation ) {
        // session()->flash( 'info_message', "スケジュールを削除します。よろしいですか。" );

        $this->authorize( 'delete', $reservation );

        BackButton::stackHere( request() );
        return view( 'groupware.reservation.delete' )->with( 'reservation' , $reservation );
    }
    
    public function deleted( Reservation $reservation ) {

        $this->authorize( 'delete', $reservation );

        $return = ReservationAction::deletes( $reservation );

        session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'info_message', "スケジュールを削除しました" );
        return redirect()->route( 'back_one' );
        // return redirect()->route( 'groupware.reservation.index' );
        // return $this->index( request() );
    }

}


