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

use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\GCalSync;
use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

class CalProp extends Model {

    // use SoftDeletes;
    
    protected $table = 'calprops';
    protected $dates = [ 'google_synced_at'];
    
    protected $fillable = [
        'user_id', 'calendar_id',

        'name', 
        'memo', 
        'background_color', 
        'text_color',
        'default_permission',
        
        // 'not_use', 
        // 'disabled', 
        
        // 'google_sync_on', 
        // 'google_sync_span', 
        // 'google_sync_level',
        // 'google_calendar_id',
        // 'google_id',
        // 'google_private_key_file_id',
        // 'google_synced_at',
    ];
    
    
    
    // protected $hidden = [];

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function user() {
        return $this->belongsTo( User::class );
    }
    
    public function calendar() {
        return $this->belongsTo( Calendar::class );
    }
    
    public function schedules() {
        return $this->calendar()->schedules();
    }
    
    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    public function gcal_syncs() {
        return hasMany( GCalSync::class, 'calprop_id' );
        
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function google_private_key_file() {
        return $this->files->first();
    }
    
    public static function default_background_color() {
        return config( 'groupware.calprop.default.background_color');
    }

    public static function default_text_color() {
        return config( 'groupware.calprop.default.text_color');
    }

    public static function default_font_weight() {
        return config( 'groupware.calprop.default.font_weight');
    }

    public static function default_font_style() {
        return config( 'groupware.calprop.default.font_style');
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  状態確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function is_filled_GoogleConfig() {
        if( $this->google_id and $this->google_calendar_id and $this->google_private_key_file() ) {
            return true;
        } 
        return false;
    }
    
    public function checkGoogleSync() {
        if( ! $this->is_filled_GoogleConfig()) { return false; }
        
        return $this->google_sync_check;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  状態設定メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function set_google_sync_check_OK() {
        $this->google_synced_at = now();
        $this->google_sync_check = 1;
        $this->save();
        return $this->save();
    }
    
    public function set_google_sync_check_NG() {
        $this->google_synced_at = null;
        $this->google_sync_check = 0;
        $this->save();
        return $this->save();
    }
    
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  フォーム用メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function style() {
        return "background-color:".$this->background_color."; color:".$this->text_color.";";
    }


}
