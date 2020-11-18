<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestMyFormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function input()
    {
        return view('helpers.myForm.test_input');
    }
}
