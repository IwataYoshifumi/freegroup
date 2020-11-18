<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\SansanRequest;
use App\User;
use App\Mail\MailOrderToAdmin;

class SansanController extends Controller
{
    //
    public function form( ) {
        
        return view( "sansan.form" );
    }
    
    public function api( SansanRequest $request ) {
        
        return view( "sansan.form" );
        
    }
}
