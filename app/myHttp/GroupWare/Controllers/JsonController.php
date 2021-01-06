<?php

namespace App\myHttp\GroupWare\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// use App\Models\Vacation\Paidleave;
// use App\Models\Vacation\ApprovalMaster;

use App\myHttp\GroupWare\Requests\JSON\GetUsersJsonRequest;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Controllers\JSON\UserJsonResponse;

class JsonController extends Controller
{
    
    public function getUsers( Request $request ) {
        return UserJsonResponse::getUsers( $request );
    }
    
    // 部署IDから社員名リストを返す
    //
    public function getUsersBlongsTo( Request $request ) {

        // $dept_id = $request->input('dept_id');

        // $users = User::where( 'dept_id', $dept_id )
        //              ->where( 'retired', false )
        //              ->get( ['id','grade','name']);
        
        // $array[''] = '';         
        // foreach( $users as $user ) {
        //     $array[ $user->id ] = $user->name." ". optional( $user )->grade;
        // }
        
        // #dd( $array );
        
        // return response()->json( $array );

        return $this->getUsers( $request );
        
    }
    
    // 　承認マスターのリストを返す
    //
    public function getApprovalMaster( Request $request ) {
        
        if( ! empty( $request->input('name'))) {
            $name = $request->input( 'name' );
            $masters = ApprovalMaster::where( 'name', 'like', '%'.$name.'%' )->get( ['id', 'name'] );
        } else {
            $masters = ApprovalMaster::all( ['id', 'name'] );
        }
        $array = array();
        
        
        foreach( $masters as $master ) {
            $array[$master->id] = $master->name;
        }
        // dd( $array );

        
        return view( 'vacation.json.jsonResponse' )->with( 'array' , $array );
        // return view( 'vacation.json.jsonTest' )->with( 'array' , $array );        
    }
    

    
}
