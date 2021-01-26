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

use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;

class ReportProp extends Model {

    // use SoftDeletes;
    
    protected $fillable = [
        'user_id', 
        'report_list_id',
        'name', 
        'memo', 
        'background_color', 
        'text_color',
        'default_permission',
        // 'not_use', 
        // 'disabled', 
        
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
    
    public function report_list() {
        return $this->belongsTo( ReportList::class );
    }
    
    public function reports() {
        return $this->report_list>reports();
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function default_background_color() {
        return config( 'groupware.report_prop.default.background_color');
    }

    public static function default_text_color() {
        return config( 'groupware.report_prop.default.text_color');
    }

    public static function default_font_weight() {
        return config( 'groupware.report_prop.default.font_weight');
    }

    public static function default_font_style() {
        return config( 'groupware.report_prop.default.font_style');
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
