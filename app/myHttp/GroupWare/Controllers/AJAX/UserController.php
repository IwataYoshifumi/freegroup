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


class UserController extends Controller   {

    public function search( Request $request ) {

        // if_debug( $request->all() );

        $user_name  = $request->user_name;
        $dept_name  = $request->dept_name;
        $grade_name = $request->grade_name;

        if( empty( $user_name ) and empty( $dept_name ) and empty( $grade_name )) { return response()->json( [] ); }
        
        //　退社社員は検索対象外
        //
        $users = User::with( 'dept' )->where( 'retired', false );

        if( $user_name  ) { $users->where( 'name', 'like', '%'. $user_name . '%' );                           }
        if( $dept_name  ) { $sub_query = Dept::select( 'id' )->where( 'name', 'like','%'. $dept_name . '%' );
                            $users->whereIn( 'dept_id', $sub_query );  }
        if( $grade_name ) { $users->where( 'grade', 'like', '%'. $grade_name . '%' ); }
        $users = $users->get();
        
        $return = [];
        foreach( $users as $user ) {
            array_push( $return, [ 'id' => $user->id, 'name' => $user->name, 'grade' => $user->grade, 'dept_name' => $user->dept->name ] );
        }

        return response()->json( $return );
    }
}