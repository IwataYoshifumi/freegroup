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
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\Http\Helpers\MyHelper;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;
use App\myHttp\GroupWare\Jobs\File\DeleteFilesJob;

class ReportListAction {
    
    // protected $table = 'groups';
    
    public static function creates( Request $request ) {

        $report_list = DB::transaction( function() use ( $request ) {
                // if_debug( $request->input() );
                $report_list = new ReportList;
                $report_list->name = $request->name;
                $report_list->memo = $request->memo;
                $report_list->type = $request->type;
                $report_list->not_use  = false;
                $report_list->disabled = false;
                $report_list->default_permission = $request->default_permission;
                $report_list->save();

                $report_list->access_lists()->sync( [$request->access_list_id] );

                //　全ユーザにReportPropを生成
                //
                $values  = [ 'report_list_id'     => $report_list->id, 
                             'name'               => $report_list->name,
                             'memo'               => $report_list->memo,
                             'text_color'         => ReportProp::default_text_color(),
                             'background_color'   => ReportProp::default_background_color(),
                             'default_permission' => $report_list->default_permission,
                            ];
                
                
                // $colors = config( 'color' );
                foreach( User::all() as $i => $user ) {
                    // $values['background_color'] = Arr::random( $colors );
                    $values['user_id'] = $user->id;
                    $report_prop = ReportProp::create( $values );
                }
                return $report_list;
            });
        
        return $report_list;
    }
    
    public static function updates( ReportList $report_list, Request $request ) {

        $report_list = DB::transaction( function() use ( $report_list, $request ) {
            
                // if_debug( $request->input() );
                $report_list->name = $request->name;
                $report_list->memo = $request->memo;
                $report_list->type = $request->type;
                $report_list->not_use  = ( $request->not_use  ) ? 1 : 0;
                $report_list->disabled = ( $request->disabled ) ? 1 : 0;
                $report_list->default_permission = $request->default_permission;
                $report_list->save();

                $report_list->access_lists()->sync( [$request->access_list_id] );

                //  ReportPropの変更種別の初期設定を更新
                //
                if( $request->init_users_default_permission ) {
                    $report_list->report_props()->update( [ 'default_permission' => $request->default_permission ] );
                }

                return $report_list;
        });
        
        return $report_list;
    }
    
    //　アクセスリストでグループを使用していたら削除不可
    //
    public static function deletes( ReportList $report_list ) {

        $files = DB::transaction( function() use ( $report_list ) {
            
            //　削除対象のデータを全て検索
            //
            $report_props = $report_list->report_props(); 
            $reports = $report_list->reports(); 
            $reportables = DB::table( 'reportables' )->whereIn( 'report_id', $reports->select( 'id' ) );
            $fileables   = DB::table( 'fileables'   )->where( 'fileable_type',   Report::class   )->whereIn( 'fileable_id',   $reports );
            $files       = MyFile::whereIn( 'id', $fileables->select( 'file_id' ))->get();

            //  ＤＢを削除
            //
            $report_props->delete();
            $reportables->delete();
            $fileables->delete();
            $reports->delete();
            $report_list->delete();
            
            return $files;
        });

        //　ファイル削除ジョブ
        //
        DeleteFilesJob::dispatch( $files );
        
        return true;
    }
    

}

