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
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\FileAction;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;


class ReportPropAction {
    
    // protected $table = 'groups';
    
    
    public static function updates( ReportProp $report_prop, Request $request ) {

        $report_prop = DB::transaction( function() use ( $report_prop, $request ) {

                $old_report_prop = clone $report_prop;

                // if_debug( $request->input() );
                $report_prop->name = $request->name;
                $report_prop->memo = $request->memo;
                $report_prop->background_color   = $request->background_color;
                $report_prop->text_color         = $request->text_color;
                $report_prop->default_permission = $request->default_permission;
                $report_prop->not_use = ( $request->not_use ) ? 1 : 0;
                $report_prop->hide    = ( $request->hide    ) ? 1 : 0;
                
                $report_prop->save();

                return $report_prop;
        });
        
        return $report_prop;
    }

}

