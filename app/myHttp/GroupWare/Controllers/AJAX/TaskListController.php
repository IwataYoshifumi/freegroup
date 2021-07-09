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
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;


class TaskListController extends Controller   {

    public function search( Request $request ) {
        
        // if_debug( $request->all() );
        
        $tasklists = TaskList::with( ['taskprops' => function( $query ) {
                    $query->where( 'user_id', user_id() );
                } ]);;

        
        //  アクセスリスト権限検索
        //
        if( $request->auth == 'owner'  ) {
            $query = TaskList::getOwner( user_id() );
        } elseif( $request->auth == 'reader' ) {
            $query = TaskList::getCanRead( user_id() );
        } else { 
            $query = TaskList::getCanWrite( user_id() );
        }
        $ids = toArray( $query, 'id' );
        $tasklists = $tasklists->whereIn('id', $ids );

        //　公開種別の検索
        //
        if( $request->types ) {
            $tasklists = $tasklists->whereIn( 'type', $request->types );
        }
        
        //　Disableの検索
        //
        if( $request->disabled ) { 
            $tasklists = $tasklists->where( 'disabled', 1 ); 
        } else {
            $tasklists = $tasklists->where( 'disabled', 0 ); 

            // if( $request->not_use  ) { 
            //     $tasklists = $tasklists->where( 'not_use',  1 ); 
            // }
        }

        //　非表示タスクの表示（taskpropを検索）
        //
        if( $request->hidden ) {
            $tasklists = $tasklists->whereHas( 'taskprops', function( $query ) {
                            $query->where( 'hide', 1 ); 
            });
        } else {
            if( ! $request->show_hidden_tasklists ) {
                $tasklists = $tasklists->whereHas( 'taskprop', function( $query ) {
                    $query->where( 'hide', 0 ); 
                });      
            }
        }
        
        if( isset( $request->tasklists ) and ! empty( $request->tasklists )) {
            $tasklists = $tasklists->orWhere( function( $query ) use ( $request ) {
                    $query->whereIn( 'id', $request->tasklists );    
            }); 
            
        }
        

        $tasklists = $tasklists->get();
        $return = [];
        foreach( $tasklists as $tasklist ) {
            $taskprop = $tasklist->taskprops->first();
            $values = [ 'id' => $tasklist->id,
                        'taskprop_id' => $taskprop->id,
                        'name' => $tasklist->name,
                        'type' => $tasklist->type,
                        'not_use' => $tasklist->not_use,
                        'disabled' => $tasklist->disabled,
                        'prop_name' => $taskprop->name,
                        'hide' => $taskprop->hide,
                        'text_color' => $taskprop->text_color,
                        'background_color' => $taskprop->background_color
            ];
            // $return[$i] = $values;
            // $i++;
            array_push( $return, $values );
            
        }

        return response()->json( $return );
        
    }
}