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

use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

class TaskProp extends Model {

    // use SoftDeletes;
    
    protected $table = 'task_props';
    
    protected $fillable = [
        'user_id', 'tasklist_id','task_list_id',

        'name', 
        'memo', 
        'background_color', 
        'text_color',
        'default_permission',
        
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
    
    public function tasklist() {
        return $this->belongsTo( TaskList::class, 'task_list_id' );
    }
    
    public function task_list() {
        return $this->tasklist();
    }
    
    public function tasks() {
        return $this->tasklist()->tasks();
    }
    
    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public static function default_background_color() {
        return config( 'groupware.taskprop.default.background_color');
    }

    public static function default_text_color() {
        return config( 'groupware.taskprop.default.text_color');
    }

    public static function default_font_weight() {
        return config( 'groupware.taskprop.default.font_weight');
    }

    public static function default_font_style() {
        return config( 'groupware.taskprop.default.font_style');
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  状態確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    

    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  フォーム用メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function style() {
        return "background-color:".$this->background_color."; color:".$this->text_color.";";
    }


    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  自分がアクセスできるTaskPropを取得
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function whereTaskListCanRead( $user ) {
        
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        
        $tasklists = TaskList::whereCanRead( $user_id )->get();
    
        $taskprops = TaskProp::whereIn( 'task_list_id', $tasklists->pluck( 'id' ))
                           ->where( 'user_id', $user_id );

        return $taskprops;
    }


}
