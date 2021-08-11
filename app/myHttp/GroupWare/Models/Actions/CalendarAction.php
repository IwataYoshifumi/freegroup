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
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\GCalSync;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

use App\myHttp\GroupWare\Models\Initialization\InitCalendar;

use App\myHttp\GroupWare\Jobs\File\DeleteFilesJob;

class CalendarAction {
    
    public static function creates( Request $request ) {

        $calendar = DB::transaction( function() use ( $request ) {
                // if_debug( $request->input() );
                $calendar = new Calendar;
                $calendar->name = $request->name;
                $calendar->memo = $request->memo;
                $calendar->type = $request->type;
                $calendar->not_use  = false;
                $calendar->disabled = false;
                $calendar->default_permission = $request->default_permission;
                $calendar->save();

                //　アクセスリスト設定
                //
                $calendar->access_lists()->sync( [$request->access_list_id] );

                //　全ユーザにCalPropを生成
                //
                InitCalendar::withCalendar( $calendar );

                return $calendar;
            });
        
        return $calendar;
    }
    
    public static function updates( Calendar $calendar, Request $request ) {

        $calendar = DB::transaction( function() use ( $calendar, $request ) {
            
                // if_debug( $request->input() );
                $calendar->name = $request->name;
                $calendar->memo = $request->memo;
                $calendar->type = $request->type;
                $calendar->not_use  = ( $request->not_use  ) ? 1 : 0;
                if( $request->disabled ) {
                    $calendar->not_use  = 1;
                    $calendar->disabled = 1;
                } else {
                    $calendar->disabled = 0;
                }
                $calendar->default_permission = $request->default_permission;
                $calendar->save();

                $calendar->access_lists()->sync( [$request->access_list_id] );

                //  CalPropの変更種別の初期設定を更新
                //
                if( $request->init_users_default_permission ) {
                    $calendar->calprops()->update( [ 'default_permission' => $request->default_permission ] );
                }
                //　変更管理者のCalPropの名前のみ変更
                //
                $calendar->calprops()->where( 'user_id', user_id() )->update( ['name' => $request->name ] );
                
                
                
                //　Googleカレンダーの同期解除
                //
                if( $calendar->disabled ) {
                    $calendar->calprops()->update( [ 'google_sync_on' => 0, 'google_sync_check' => 0 ]);
                }

                return $calendar;
        });
        
        return $calendar;
    }
    
    public static function deletes( Calendar $calendar ) {

        $files = DB::transaction( function() use ( $calendar ) {

            //　削除対象のデータを全て検索
            //
            $calprops = $calendar->calprops(); 
            $gcal_syncs = GCalSync::whereIn( 'calprop_id', $calprops->select('id') );  
            $schedules = $calendar->schedules(); 
            
            $sub_query_1 = clone $schedules;
            $sub_query_1->select( 'id' );
            $scheduleables = DB::table( 'scheduleables' )->whereIn( 'schedule_id', $sub_query_1 );

            $reportables = DB::table( 'reportables' )->where( 'reportable_type', Schedule::class )->whereIn( 'reportable_id', $sub_query_1 );
            $fileables   = DB::table( 'fileables'   )->where( 'fileable_type',   Schedule::class )->whereIn( 'fileable_id',   $sub_query_1 );
            
            $sub_query_2 = clone $fileables;
            $files       = MyFile::whereIn( 'id', $sub_query_2->select( 'file_id' ))->get();

            // if_debug( $fileables->get()->toArray(), $files->get()->toArray() );
            
            //  ＤＢを削除
            //
            
            $gcal_syncs->delete();
            $calprops->delete();
            $scheduleables->delete();
            $reportables->delete();
            $fileables->delete();
            $schedules->delete();
            $calendar->access_lists()->detach();
            $calendar->delete();
            
            return $files;
        });

        //　ファイル削除ジョブ
        //
        DeleteFilesJob::dispatch( $files );

        return true;
    }
    

}

