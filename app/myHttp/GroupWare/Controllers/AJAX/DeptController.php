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


class DeptController extends Controller   {

    public function search( Request $request ) {

        if( empty( $request->dept_name )) { return response()->json( [] ); }

        $depts = Dept::where( 'name', 'like', '%'. $request->dept_name . '%' )->get();

        return response()->json( $depts->pluck( 'name', 'id' )->toArray() );
    }
}