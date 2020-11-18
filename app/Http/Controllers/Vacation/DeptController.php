<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

use App\Http\Controllers\DeptController as OriginalDeptController;

use App\Models\Vacation\Dept;
use App\Http\Requests\Vacation\DeptRequest;
use App\Http\Helpers\BackButton;

class DeptController extends OriginalDeptController {

}
