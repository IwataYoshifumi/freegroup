<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

class SearchFile {
    
    static public function search( Request $request ) {

        if( ! $request->find ) { return []; }
        
        $files = new MyFile;
        $pagination = ( $request->pagination ) ? $request->pagination : 20;
    
        if( $request->start_date ) {
            $files = $files->where( 'created_at', '>=', $request->start_date ." 00:00" );
        }
        if( $request->end_date ) {
            $files = $files->where( 'created_at', '<=', $request->end_date ." 23:59:59" );
        }
        if( $request->file_name ) {
            $files = $files->where( 'file_name', 'like', '%'. $request->ile_name . '%' );
        }
        
        if( is_debug() and $request->users and is_array( $request->users )) {
            $files = $files->whereIn( 'user_id', $request->users );
        } else {
            $files = $files->where( 'user_id', user_id() );
        }

        // 添付のあり・なし
        //
        if( $request->attached == 1 ) {          // 添付あり
            $files = $files->has( 'fileables' );

        } elseif( $request->attached == -1 ) {   // 添付なし
            $files = $files->doesntHave( 'fileables' );

        }
        $files = $files->with( 'user', 'schedules', 'reports', 'calprops' )->paginate( $pagination );
        
        return $files;
        
    }
}