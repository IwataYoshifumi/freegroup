<?php

namespace App\myHttp\GroupWare\Controllers\AJAX;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;


class CalendarController extends Controller   {

    public function search( Request $request ) {
        
        // if_debug( $request->all() );
        
        $calendars = Calendar::with( 'calprop' );
        
        //  アクセスリスト権限検索
        //
        // if( $request->auth == 'owner' ) {
        if( $request->calendar_permission == 'owner' ) {
            $query = Calendar::getOwner( user_id() );
        } elseif( $request->auth == 'reader' ) {
            $query = Calendar::getCanRead( user_id() );
        } else { 
            $query = Calendar::getCanWrite( user_id() );
        }
        $ids = toArray( $query, 'id' );
        $calendars = $calendars->whereIn('id', $ids );

        //　公開種別の検索
        //
        if( $request->types ) {
            $calendars = $calendars->whereIn( 'type', $request->types );
        }
        
        //　Disableの検索
        //
        if( ! $request->show_disabled ) { 
            $calendars = $calendars->where( 'disabled', 0 ); 
        }

        //　非表示タスクの表示（calpropを検索）
        //
        
        if( ! $request->show_hidden_calendars ) {
            $calendars = $calendars->whereHas( 'calprop', function( $query ) {
                            $query->where( 'hide', 0 );
            });
        }

        // dump( $calendars );
        $calendars = $calendars->get();
        $return = [];
        // dump( $calendars );
        
        foreach( $calendars as $calendar ) {
            $calprop = $calendar->my_calprop();
            $values = [ 'id' => $calendar->id,
                        'calprop_id' => $calprop->id,
                        'name' => $calendar->name,
                        'type' => $calendar->type,
                        'not_use' => $calendar->not_use,
                        'disabled' => $calendar->disabled,
                        'prop_name' => $calprop->name,
                        'hide' => $calprop->hide,
                        'text_color' => $calprop->text_color,
                        'background_color' => $calprop->background_color
            ];
            // $return[$i] = $values;
            // $i++;
            array_push( $return, $values );
        }

        return response()->json( $return );
        
    }
}