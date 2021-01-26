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
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\FileAction;

use App\myHttp\GroupWare\Requests\ReportRequest;

class ReportAction  {
    
    // Reportの新規作成
    //
    public static function creates( ReportRequest $request ) {

        $report = DB::transaction( function() use ( $request ) {
            
            $report = new Report;
            $report->user_id     = user_id();
            $report->updator_id  = user_id();
            $report->report_list_id = $request->report_list_id;
            $report->permission  = $request->permission;
            

            $report->name  = $request->name;
            $report->memo  = $request->memo;
            $report->place = $request->place;

            $report->start      = $request->start;
            $report->start_date = $request->start_date;
            $report->end        = $request->end;
            $report->end_date   = $request->end_date;

            // $report->start_time = ( ! $request->all_day ) ? $request->start_time : '';
            // $report->end_time   = ( ! $request->all_day ) ? $request->end_time   : '';
            $report->all_day    = (   $request->all_day ) ? 1 : 0;
            $report->save();
            
            // リレーションの同期      
            //
            $report->users()->sync(     $request->users     );
            $report->customers()->sync( $request->customers );
            $report->schedules()->sync( $request->schedules );
            $report->files()->sync( $request->attach_files );

            return $report;

        });    
        return $report;
    }
    
    // Reportの修正
    //
    public static function updates( Report $report, ReportRequest $request ) {

        $report = DB::transaction( function() use ( $report, $request ) {

            $user = auth( 'user' )->user();

            // カレンダーID・変更権限（permission）は作成者のみ変更可能
            if( $user->id == $report->user_id ) {
                $report->report_list_id = $request->report_list_id;
                $report->permission  = $request->permission;
            }
            $report->updator_id  = $user->id;

            $report->name  = $request->name;
            $report->memo  = $request->memo;
            $report->place = $request->place;

            $report->start      = $request->start;
            $report->start_date = $request->start_date;
            $report->end        = $request->end;
            $report->end_date   = $request->end_date;

            // $report->start_time = ( ! $request->all_day ) ? $request->start_time : '';
            // $report->end_time   = ( ! $request->all_day ) ? $request->end_time   : '';
            $report->all_day    = ( $request->all_day ) ? 1 : 0;
            $report->save();
            
            // リレーションの同期  
            //
            $report->users()->sync(     $request->users     );
            $report->customers()->sync( $request->customers );
            $report->schedules()->sync( $request->schedules );
            
            $report->files()->sync( $request->attach_files );

            return $report;
        });    
        return $report;
    }
    
    // Reportの削除
    //
    public static function deletes( $report ) { 

        $files = DB::transaction( function() use ( $report ) {
            
            $report->attendees()->detach();
            $report->customers()->detach();
            $report->schedules()->detach();
            $files = $report->files;
            $report->files()->detach();
            $report->delete();

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

