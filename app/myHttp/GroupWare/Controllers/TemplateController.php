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

// use App\myHttp\Schedule\Request\ScheduleRequest;
// use App\myHttp\Schedule\Models\MyModels;
use App\Models\Customer;

class TemplateController extends Controller {

    public function index( Request $request ) {
        $find = [];
        $show = [];

        return view( 'groupware.calendar.index')->with( 'find', $find )
                                               ->with( 'show', $show );
    }

    public function show() {
        
    }
    
    public function detail() {
        
    }
    
    
    public function create() {
        
    }
    
    public function store() {
        
    }

    public function edit() {
        
    }
    
    public function update() {
        
    }
    
    public function delete() {
        
    }

    public function deleted() {
        
    }
    
}
