<?php

namespace App\Http\Controllers\Vacation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Paidleave;
use App\Models\Vacation\ApprovalMaster;

class JsonController extends Controller
{
    // 部署名から社員名リストを返す
    //
    public function getUsersBlongsTo( Request $request ) {

        $dept_id = $request->input('dept_id');

        $users = User::where( 'dept_id', $dept_id )
                     ->where( 'retired', false )
                     ->get( ['id','grade','name']);
        
        $array[''] = '';         
        foreach( $users as $user ) {
            $array[ $user->id ] = $user->name." ".$user->grade;
        }
        
        #dd( $array );
        
        return view( 'vacation.json.jsonResponse' )->with( 'array' , $array );
        
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
