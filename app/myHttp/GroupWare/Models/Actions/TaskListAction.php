<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Arr;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

use App\myHttp\GroupWare\Models\Initialization\InitTaskList;

use App\myHttp\GroupWare\Jobs\File\DeleteFilesJob;

class TaskListAction {
    
    public static function creates( Request $request ) {

        $tasklist = DB::transaction( function() use ( $request ) {
                // if_debug( $request->input() );
                $tasklist = new TaskList;
                $tasklist->name = $request->name;
                $tasklist->memo = $request->memo;
                $tasklist->type = $request->type;
                $tasklist->not_use  = false;
                $tasklist->disabled = false;
                $tasklist->default_permission = $request->default_permission;
                $tasklist->save();

                //　アクセスリスト設定
                //
                $tasklist->access_lists()->sync( [$request->access_list_id] );

                //　全ユーザにTaskPropを生成
                //
                InitTaskList::withTaskList( $tasklist );

                return $tasklist;
            });
        
        return $tasklist;
    }
    
    public static function updates( TaskList $tasklist, Request $request ) {

        $tasklist = DB::transaction( function() use ( $tasklist, $request ) {
            
                // if_debug( $request->input() );
                $tasklist->name = $request->name;
                $tasklist->memo = $request->memo;
                $tasklist->type = $request->type;
                $tasklist->not_use  = ( $request->not_use  ) ? 1 : 0;
                if( $request->disabled ) {
                    $tasklist->not_use  = 1;
                    $tasklist->disabled = 1;
                } else {
                    $tasklist->disabled = 0;
                }
                $tasklist->default_permission = $request->default_permission;
                $tasklist->save();

                $tasklist->access_lists()->sync( [$request->access_list_id] );

                //  TaskPropの変更種別の初期設定を更新
                //
                if( $request->init_users_default_permission ) {
                    $tasklist->taskprops()->update( [ 'default_permission' => $request->default_permission ] );
                }
                //　変更管理者のTaskPropの名前のみ変更
                //
                $tasklist->taskprops()->where( 'user_id', user_id() )->update( ['name' => $request->name ] );


                return $tasklist;
        });
        
        return $tasklist;
    }
    
    public static function deletes( TaskList $tasklist ) {

        $files = DB::transaction( function() use ( $tasklist ) {

            //　削除対象のデータを全て検索
            //
            $taskprops = $tasklist->taskprops(); 
            $tasks = $tasklist->tasks(); 
            
            $sub_query_1 = clone $tasks;
            $sub_query_1->select( 'id' );
            $taskables = DB::table( 'taskables' )->whereIn( 'task_id', $sub_query_1 );

            // $reportables = DB::table( 'reportables' )->where( 'reportable_type', Task::class )->whereIn( 'reportable_id', $sub_query_1 );
            $fileables   = DB::table( 'fileables'   )->where( 'fileable_type',   Task::class )->whereIn( 'fileable_id',   $sub_query_1 );
            
            $sub_query_2 = clone $fileables;
            $files       = MyFile::whereIn( 'id', $sub_query_2->select( 'file_id' ))->get();

            // if_debug( $fileables->get()->toArray(), $files->get()->toArray() );
            
            //  ＤＢを削除
            //
            $taskprops->delete();
            $taskables->delete();
            // $reportables->delete();
            $fileables->delete();
            $tasks->delete();
            $tasklist->delete();
            
            return $files;
        });

        //　ファイル削除ジョブ
        //
        DeleteFilesJob::dispatch( $files );

        return true;
    }
    

}

