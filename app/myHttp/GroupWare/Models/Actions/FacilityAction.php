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
use App\myHttp\GroupWare\Models\Facility;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\FileAction;

class FacilityAction  {
    
    // Facilityの新規作成
    //
    public static function creates( Request $request ) {

        $facility = DB::transaction( function() use ( $request ) {
            
            $facility = new Facility;
            $facility->name             = $request->name;
            $facility->memo             = $request->memo;
            $facility->location         = $request->location;
            $facility->control_number   = $request->control_number;
            $facility->category         = $request->category;
            $facility->sub_category     = $request->sub_category;
            $facility->type             = $request->type;
            $facility->background_color = $request->background_color;
            $facility->text_color       = $request->text_color;
            
            $facility->save();
            
            $facility->access_lists()->sync( [$request->access_list_id] );
            
            $facility->files()->sync( $request->attach_files );

            return $facility;

        });    
        return $facility;
    }
    
    // Facilityの修正
    //
    public static function updates( Facility $facility, Request $request ) {

        $facility = DB::transaction( function() use ( $facility, $request ) {

            $facility->name             = $request->name;
            $facility->memo             = $request->memo;
            $facility->location         = $request->location;
            $facility->control_number   = $request->control_number;
            $facility->category         = $request->category;
            $facility->sub_category     = $request->sub_category;
            $facility->type             = $request->type;
            $facility->background_color = $request->background_color;
            $facility->text_color       = $request->text_color;
            $facility->disabled         = ( $request->disabled ) ? 1 : 0;
            $facility->save();

            $facility->files()->sync( $request->attach_files );

            $facility->access_lists()->sync( [$request->access_list_id] );

            return $facility;
        });    
        return $facility;
    }
    
    // Facilityの削除
    //
    public static function deletes( $facility ) { 

        $files = DB::transaction( function() use ( $facility ) {

            $facility->access_lists()->detach();
            
            $files = $facility->files;
            $facility->files()->detach();
            $facility->reservations()->delete();
            $facility->delete();
            
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

