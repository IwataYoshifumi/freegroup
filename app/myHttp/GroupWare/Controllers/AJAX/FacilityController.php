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
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;


class FacilityController extends Controller   {

    public function search( Request $request ) {
        
        // if_debug( $request->all() );
        
        //  アクセスリスト権限検索
        //
        if( $request->facility_permission == 'owner' ) {
            $query = Facility::getOwner( user_id() );
        } elseif( $request->facility_permission == 'reader' ) {
            $query = Facility::getCanRead( user_id() );
        } else { 
            $query = Facility::getCanWrite( user_id() );
        }
        $ids = toArray( $query, 'id' );
        $facilities = Facility::whereIn('id', $ids );
        
        //　公開種別の検索
        //
        if( $request->types ) {
            $facilities = $facilities->whereIn( 'type', $request->types );
        }
        
        //　Disableの検索
        //
        if( ! $request->show_disabled ) { 
            $facilities = $facilities->where( 'disabled', 0 ); 
        }

        //　大分類で検索
        //
        if( is_array( $request->categories )) {
            
            $facilities = $facilities->whereIn( 'category', $request->categories );
        }


        // 選択されている設備も検索
        //
        if( isset( $request->facilities ) and ! empty( $request->facilities )) {
            $facilities = $facilities->orWhere( function( $query ) use ( $request ) { 
                $query->whereIn( 'id', $request->facilities );
            }); 
        }
        
        

        // dump( $facilities );
        $facilities = $facilities->get();
        $return = [];
        // dump( $facilities );
        
        foreach( $facilities as $facility ) {
            $values = [ 'id' => $facility->id,
                        'name' => $facility->name,
                        'type' => $facility->type,
                        'category' => $facility->category,
                        'sub_category' => $facility->sub_category,
        
                        // 'not_use' => $facility->not_use,
                        'disabled' => $facility->disabled,
                        'text_color' => $facility->text_color,
                        'background_color' => $facility->background_color
            ];
            // $return[$i] = $values;
            // $i++;
            array_push( $return, $values );
        }

        return response()->json( $return );
        
    }
}