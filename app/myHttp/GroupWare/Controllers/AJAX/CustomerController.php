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
use App\myHttp\GroupWare\Models\Customer;


class CustomerController extends Controller   {

    public function search( Request $request ) {

        if( empty( $request->customer_name )) { return response()->json( [] ); }

        $customers = Customer::where( 'name', 'like', '%'. $request->customer_name . '%' )->get();

        return response()->json( $customers->pluck( 'name', 'id' )->toArray() );
    }
}