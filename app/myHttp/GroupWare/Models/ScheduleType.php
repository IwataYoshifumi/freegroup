<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

class ScheduleType extends Model {

    // use SoftDeletes;
    
    protected $fillable = [
        'name', 'user_id', 'google_calendar_id', 'google_id', 'google_private_key_file', 'color', 'text_color', 'class',
    ];
    // protected $hidden = [];

    // スケジュール種別のクラス
    //
    // デフォルトは削除できない。　　（初期に自動作成・削除不可）
    // 関連は他人に関連付けられた予定（初期に自動作成・削除不可）
    // オリジナルは社員が自由に作成可（削除も可）
    // 今後の機能拡張クラス
    // 休暇　　（初期に自動作成・削除不可）
    // 有給休暇（初期に自動作成・削除不可）
    //
    const DEFAULT_CLASS     = 'default';
    const RELATION_CLASS    = 'relation';
    const ORIGINAL_CLASS    = 'original';
    
    const DEFAULT_CLASSES = [ 'default', 'relation' ];


    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    // 作成者
    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }
    
    public function schedules() {
        return $this->hasMany( Schedule::class );
        
    }
    
    public function files() {
        // return $this->belongsTo( MyFile::class );
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    // Google 認証キーの取得
    public function google_private_key_file() {
        // スケジュール種別に添付されるファイルはGoogle のみ
        return $this->files()->first();
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //　確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function isset_google_private_key_file() {
        if( ! is_null( $this->google_private_key_file() )) { 
            return true;
        } else {
            return false;
        }
    }
    
    public function isset_google_calendar() {
        if( ! empty( $this->google_calendar_id ) and ! empty( $this->google_id ) and ! empty( $this->isset_google_private_key_file())) {
            return true;
        } else {
            return false;
        }
        
    }
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  フォーム用メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public static function get_array_for_select( $user_id ) {
        $types = User::find( $user_id )->schedule_types()->where( 'class','!=', 'relation' )->get();
        
        $array = [];
        foreach( $types as $type ) {
            $array[$type->id] = $type->name;
        }
        return $array;
    }
    
    // 関連付けスケジュールのスケジュール種別を取得
    public static function get_schedule_type_of_relation_class( $user_id ) {
        return User::find( $user_id )->schedule_types()->where( 'class', 'relation' )->first();
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  デフォルトスケジュール種別の作成
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    //　デフォルトのスケジュール種別が登録されているかチェックし、初期化されていなければ、
    //  初期化（デフォルトのスケジュール種別の登録）を行う。
    //
    //  返値：　初期化を行ったらtrue、初期化しなければ false 
    
    public static function init_schedule_types( User $user ) {
        $checks = [];
        foreach( $user->schedule_types as $type ) {
            array_push( $checks, $type->class );
        }
        $deficiency = array_diff( self::DEFAULT_CLASSES, $checks );
        
        $return = false;
        if( in_array( 'default', $deficiency  )) { self::create_default_schedule_type( $user );  $return = true; }
        if( in_array( 'relation', $deficiency )) { self::create_relation_schedule_type( $user ); $return = true; }
        
        return $return;
    }
    
    public static function create_default_schedule_type( User $user ) {
        
        $schedule_type = new ScheduleType;
        // dump( 'aaa');
        
        $schedule_type->user_id = $user->id;
        $schedule_type->name    = "スケジュール";
        $schedule_type->class   = "default"; 
        $schedule_type->google_calendar_id = null;
        $schedule_type->google_id = null;
        $schedule_type->color = "#dddddd";
        $schedule_type->text_color = "#000000";
        
        $schedule_type->save();
        
        return $schedule_type;
    }
    
    public static function create_relation_schedule_type( User $user ) {

        $schedule_type = new ScheduleType;
        
        $schedule_type->user_id = $user->id;
        $schedule_type->name    = "関連付け予定";
        $schedule_type->class   = "relation";
        $schedule_type->google_calendar_id = null;
        $schedule_type->google_id = null;
        $schedule_type->color = "#ffffff";
        $schedule_type->text_color = "#666666";
        
        $schedule_type->save();
        
        return $schedule_type;
    }


}
