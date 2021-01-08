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
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

class ScheduleAction  {
    
    // Scheduleの新規作成
    public static function creates( Request $request ) {

        $schedule = DB::transaction( function() use ( $request ) {
            
            $schedule = new Schedule;
            $schedule->user_id     = user_id();
            $schedule->updator_id  = user_id();
            $schedule->calendar_id = $request->calendar_id;
            $schedule->permission  = $request->permission;
            

            $schedule->name  = $request->name;
            $schedule->memo  = $request->memo;
            $schedule->place = $request->place;

            $schedule->start      = $request->start;
            $schedule->start_date = $request->start_date;
            $schedule->end        = $request->end;
            $schedule->end_date   = $request->end_date;

            // $schedule->start_time = ( ! $request->all_day ) ? $request->start_time : '';
            // $schedule->end_time   = ( ! $request->all_day ) ? $request->end_time   : '';
            $schedule->all_day    = (   $request->all_day ) ? 1 : 0;
            $schedule->save();
            
            // 関連顧客・社員情報の同期        
            //
            $schedule->customers()->sync( $request->customers );
            $schedule->users()->sync( $request->users );
            
            // //　ファイル保存
            // //
            // $files = [];
            // foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
            //     // dump( "aaa", $i, $file );
            //     $path = $file->store('');
            //     $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->user()->id ];
            //     $f = MyFile::create( $value );
            //     $files[$i] = $f->id;
            // }
            $schedule->files()->sync( $request->attach_files );

            return $schedule;

        });    
        return $schedule;
    }
    
    // Scheduleの修正
    public static function updates( Schedule $schedule, Request $request ) {

        $schedule = DB::transaction( function() use ( $schedule, $request ) {

            $user = auth( 'user' )->user();

            // カレンダーID・変更権限（permission）は作成者のみ変更可能
            if( $user->id == $schedule->user_id ) {
                $schedule->calendar_id = $request->calendar_id;
                $schedule->permission  = $request->permission;
            }
            $schedule->updator_id  = $user->id;

            $schedule->name  = $request->name;
            $schedule->memo  = $request->memo;
            $schedule->place = $request->place;

            $schedule->start      = $request->start;
            $schedule->start_date = $request->start_date;
            $schedule->end        = $request->end;
            $schedule->end_date   = $request->end_date;

            // $schedule->start_time = ( ! $request->all_day ) ? $request->start_time : '';
            // $schedule->end_time   = ( ! $request->all_day ) ? $request->end_time   : '';
            $schedule->all_day    = ( $request->all_day ) ? 1 : 0;
            $schedule->save();
            
            // 関連顧客・社員情報の同期        
            //
            $schedule->customers()->sync( $request->customers );
            $schedule->users()->sync( $request->users );
            
            //　アップロードファイル
            //
            // $files = ( ! empty( $request->attached_files )) ? $request->attached_files : [] ;
            // // dd( $request->file( 'upload_files' ));
            // foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
            //     // dump( "aaa", $i, $file );
            //     $path = $file->store('');
            //     $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->user()->id ];
            //     $f = MyFile::create( $value );
            //     // $files[$i] = $f->id;
            //     array_push( $files, $f->id );
            // }
            // // dd( $files );
            // $schedule->files()->sync( $files );
            $schedule->files()->sync( $request->attach_files );

            return $schedule;
        });    
        return $schedule;
    }
    
    // Scheduleの削除
    public static function deletes( $schedule ) { 

        $return = DB::transaction( function() use ( $schedule ) {
            dump( __METHOD__, $schedule );
            
            $schedule->attendees()->detach();
            $schedule->customers()->detach();
            $schedule->reports()->detach();
            $schedule->files()->detach();
            
            return $schedule->delete();
        });
        
        return $return;
    }
    
}

