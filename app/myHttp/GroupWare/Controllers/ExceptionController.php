<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;

class ExceptionController extends Controller {

    public function noAuthRoute() {
        BackButton::stackHere( request() );
        return view( 'noauth' );
    }
    
}
