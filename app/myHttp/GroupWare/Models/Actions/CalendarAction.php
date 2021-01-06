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

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class CalendarAction {
    
    // protected $table = 'groups';
    
    //　検索
    //
    public static function search( $find ) {
    }

    public static function creates( Request $request ) {

        $calendar = DB::transaction( function() use ( $request ) {
                // dump( $request->input() );
                $calendar = new Calendar;
                $calendar->name = $request->name;
                $calendar->memo = $request->memo;
                $calendar->type = $request->type;
                $calendar->not_use  = false;
                $calendar->disabled = false;
                $calendar->default_permission = $request->default_permission;
        
                $calendar->save();

                $calendar->access_lists()->sync( [$request->access_list_id] );

                //　全ユーザにCalPropを生成
                //
                $values = [ 'calendar_id'        => $calendar->id, 
                            'name'               => $calendar->name,
                            'memo'               => $calendar->memo,
                            'text_color'         => CalProp::default_text_color(),
                            'background_color'   => CalProp::default_background_color(),
                            'default_permission' => $calendar->default_permission,
                            ];
                
                $colors = config( 'color' );
                foreach( User::all() as $i => $user ) {
                    // $values['background_color'] = Arr::random( $colors );                    
                    $values['user_id']          = $user->id;
                    $calprop = CalProp::create( $values );
                }
                dump( $values );
                return $calendar;
            });
        // dd( $calendar->calprops->first() );
        
        return $calendar;
    }
    
    public static function updates( Calendar $calendar, Request $request ) {

        $calendar = DB::transaction( function() use ( $calendar, $request ) {
            
                // dump( $request->input() );
                $calendar->name = $request->name;
                $calendar->memo = $request->memo;
                $calendar->type = $request->type;
                $calendar->not_use  = ( $request->not_use  ) ? 1 : 0;
                $calendar->disabled = ( $request->disabled ) ? 1 : 0;
                $calendar->default_permission = $request->default_permission;
                $calendar->save();

                $calendar->access_lists()->sync( [$request->access_list_id] );

                //  CalPropの変更種別の初期設定を更新
                //
                if( $request->init_users_default_permission ) {
                    $calendar->calprops()->update( [ 'default_permission' => $request->default_permission ] );
                }

                return $calendar;
        });
        
        return $calendar;
    }
    
    //　アクセスリストでグループを使用していたら削除不可
    //
    public static function deletes( Calendar $calendar ) {

        $calendar = DB::transaction( function() use ( $calendar ) {
            });
        
        return $calendar;
    }
    

}

