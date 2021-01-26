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
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\FileAction;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;


class CalPropAction {
    
    // protected $table = 'groups';
    
    
    public static function updates( CalProp $calprop, Request $request ) {

        $calprop = DB::transaction( function() use ( $calprop, $request ) {

                $old_calprop = clone $calprop;
                $old_file = $calprop->google_private_key_file();

                // if_debug( 'old_file', op( $old_file )->id, op( $old_file)->file_name );
                // if_debug( $request->input() );
                
                $calprop->name = $request->name;
                $calprop->memo = $request->memo;
                $calprop->background_color   = $request->background_color;
                $calprop->text_color         = $request->text_color;
                $calprop->default_permission = $request->default_permission;
                
                $calprop->not_use = ( $request->not_use ) ? 1 : 0;
                $calprop->hide    = ( $request->hide    ) ? 1 : 0;
                
                $calprop->google_sync_bidirectional = $request->google_sync_bidirectional;
                $calprop->google_calendar_id = $request->google_calendar_id;
                $calprop->google_id          = $request->google_id;
                $calprop->google_sync_level  = $request->google_sync_level;
                $calprop->google_sync_span   = $request->google_sync_span;

                //　Googleプライベートキーファイルのアップロード
                //
                if( isset( $request->google_private_key_file )) {
                    $file = FileAction::save( $request->google_private_key_file );
                    $calprop->files()->sync( $file );

                    $google_private_key_file_has_changed = 1;
                    
                    if( $old_file instanceof MyFile ) { FileAction::force_delete( $old_file ); }
                    
                }

                // Google同期関連の設定を変更したら同期は一旦解除される
                // 
                if( $calprop->google_id          !== $old_calprop->google_id or
                    $calprop->google_calendar_id !== $old_calprop->google_calendar_id or
                    $calprop->google_sync_bidirectional != $old_calprop->google_sync_bidirectional or 
                    isset( $google_private_key_file_has_changed )  ) {
                    
                        $calprop->google_sync_on = 0;
                        $calprop->google_sync_check = 0;
                        $calprop->google_synced_at = null;

                    }
                $calprop->save();

                return $calprop;
        });
        
        return $calprop;
    }

}

