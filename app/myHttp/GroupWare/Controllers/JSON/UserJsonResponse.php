<?php
namespace App\myHttp\GroupWare\Controllers\JSON;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;


class UserJsonResponse  {
    
        public static function getUsers( Request $request ) {

        $dept_id = $request->input('dept_id');
        $name    = $request->input('name');

        // 入力チェック
        if( empty( $dept_id ) and empty( $name )) { return response()->json( [] ); }


        $query = User::where( 'retired', false );
        if( ! empty( $dept_id)) {
            $query->where( 'dept_id', $dept_id ); 
        }
        if( ! empty( $name )) {
            $query->where( 'name', 'like', '%'.$name.'%' );
        }
        

        $users = $query->with( ['dept'] )->get();
        
        $array[''] = '';         
        foreach( $users as $user ) {
            $grade = ( $user->grade ) ? $user->grade : "";
            
            if( ! empty( $dept_id )) {
                $value = $user->name . " " . $grade;
            } else {
                $dept = ( $user->dept->name ) ? "【". $user->dept->name ."】" : "";
                $value = $dept . " ". $user->name . " ". $user->grade;
            }
            $array[$user->id] = $value;
        }
        
        return response()->json( $array );
        
    }
}