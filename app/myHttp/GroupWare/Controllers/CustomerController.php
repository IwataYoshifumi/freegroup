<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\Models\Customer as OriginalCustomer;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;

use App\Http\Controllers\Customer\CustomerController as OriginalCustomerController;

class CustomerController extends OriginalCustomerController {
    
    public function show( OriginalCustomer $customer ) {
        return self::detail( $customer->id );
    }

    public function detail( Customer $customer, Request $request ) {
        Backbutton::stackHere( $request );
        return view( 'groupware.customer.detail' )->with( 'customer', $customer );
    }
    
    public function deleted( OriginalCustomer $customer ) {
        $customer = Customer::find( $customer->id );
        
        DB::transaction( function() use( $customer ) {
            
            $customer->schedules()->detach();
            $customer->reports()->detach();
            $customer->delete();
        });
        
        // $customer->delete();

        session()->regenerateToken();
        BackButton::removePreviousSession();
        return view( 'customer.delete')->with( 'customer', $customer );
        
    }
    
}
