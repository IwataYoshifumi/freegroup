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
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\myHttp\GroupWare\Controllers\Search\SearchReportList;

class ReportListController extends Controller   {

    public function search( Request $request ) {

        $report_lists = SearchReportList::search( $request );

        $return = [];
        foreach( $report_lists as $r ) {
            array_push( $return,  [  
                'id'    => $r->id, 
                'name'  => $r->name,
                'type'  => $r->type,
            ] );
        }
        
        // if_debug( $request->all(), $report_lists->toArray(), $return );
        
        return response()->json( $return );
    }
}