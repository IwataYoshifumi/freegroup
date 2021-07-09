<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Arr;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\FileAction;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;


class TaskPropAction {
    
    // protected $table = 'groups';
    
    
    public static function updates( TaskProp $taskprop, Request $request ) {

        $taskprop = DB::transaction( function() use ( $taskprop, $request ) {

                $old_taskprop = clone $taskprop;

                // if_debug( $request->input() );
                $taskprop->name = $request->name;
                $taskprop->memo = $request->memo;
                $taskprop->background_color   = $request->background_color;
                $taskprop->text_color         = $request->text_color;
                $taskprop->default_permission = $request->default_permission;
                $taskprop->not_use = ( $request->not_use ) ? 1 : 0;
                $taskprop->hide    = ( $request->hide    ) ? 1 : 0;
                
                $taskprop->save();

                return $taskprop;
        });
        
        return $taskprop;
    }

}

