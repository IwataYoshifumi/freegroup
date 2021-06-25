<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\FileAction;

use App\myHttp\GroupWare\Requests\TaskRequest;

class TaskAction  {
    
    // Taskの新規作成
    //
    public static function creates( TaskRequest $request ) {

        $task = DB::transaction( function() use ( $request ) {
            
            $task = new Task;
            $task->user_id     = user_id();
            $task->updator_id  = user_id();
            $task->tasklist_id = $request->tasklist_id;
            $task->permission  = $request->permission;

            $task->name  = $request->name;
            $task->memo  = $request->memo;

            $task->due_date = $request->due_date;
            $task->due_time = ( $request->all_day ) ? new Carbon( $request->due_date ) : new Carbon( $request->due_date . " ". $request->due_time );
            $task->all_day  = ( $request->all_day ) ? 1 : 0;
            
            $task->status = "未完";

            $task->save();
            
            // リレーションの同期      
            //
            $task->users()->sync(     $request->users     );
            $task->customers()->sync( $request->customers );
            $task->files()->sync( $request->attach_files );
            // $task->schedules()->sync( $request->schedules );


            return $task;

        });    
        return $task;
    }
    
    // Taskの修正
    //
    public static function updates( Task $task, TaskRequest $request ) {

        $task = DB::transaction( function() use ( $task, $request ) {

            $user = auth( 'user' )->user();

            // 変更権限（permission）は作成者のみ変更可能
            if( $user->id == $task->user_id ) {
                $task->permission  = $request->permission;
            }
            $task->updator_id  = $user->id;

            $task->name  = $request->name;
            $task->memo  = $request->memo;

            $task->due_date = $request->due_date;
            $task->due_time = ( $request->all_day ) ? new Carbon( $request->due_date ) : new Carbon( $request->due_date . " ". $request->due_time );
            $task->all_day  = ( $request->all_day ) ? 1 : 0;
            
            $task->save();
            
            // リレーションの同期  
            //
            $task->users()->sync( $request->users );
            $task->customers()->sync( $request->customers );
            // $task->schedules()->sync( $request->schedules );
            $task->files()->sync( $request->attach_files );

            return $task;
        });    
        return $task;
    }
    
    // Taskの削除
    //
    public static function deletes( $task ) { 

        $files = DB::transaction( function() use ( $task ) {
            
            $task->attendees()->detach();
            $task->customers()->detach();
            // $task->schedules()->detach();
            $files = $task->files;
            $task->files()->detach();
            $task->delete();

            return $files;
        });
        
        //　関連ファイルを削除
        //
        foreach( $files as $file ) {
            FileAction::force_delete( $file );
        }
        
        return true;
    }
    
}

