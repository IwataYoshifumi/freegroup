<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Fileable;

class File extends Model {
    
    public $fillables;
    
    protected $fillable = [
                'file_name', 'path', 'user_id',
            ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////

    //　ファイル所有者
    //
    public function user() {
        return $this->belongsTo( User::class );
    }

    public function reports() {
        return $this->morphedByMany( Report::class, 'fileable' );
    }
    
    public function schedules() {
        return $this->morphedByMany( Schedule::class, 'fileable' );
    }

    public function calprops() {
        // 添付ファイルはGoogleの秘密鍵（添付は１つのみ）
        return $this->morphedByMany( CalProp::class, 'fileable' );
    }

    public function facilities() {
        return $this->morphedByMany( Facility::class, 'fileable' );
    }


    public function fileables() {
        return $this->hasMany( Fileable::class, 'file_id' );
    }
    

    //////////////////////////////////////////////////////////////////////////
    //
    //  DBクエリーメソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public static function whereAttached() {
        return self::where( function( $query ) {
                $query->has( 'reports' )
                      ->orHas( 'schedules' )
                      ->orHas( 'calprops'  );
            });
    }

    public static function whereDoentAttached() {
        return self::where( function( $query ) {
                    $query->doesntHave( 'reports' )
                          ->doesntHave( 'schedules' )
                          ->doesntHave( 'calprops'  );            
            });
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    //////////////////////////////////////////////////////////////////////////

    public function calprop() {
        // 添付ファイルはGoogleの秘密鍵（添付は１つのみ）
        return $this->calprops->first();
    }

    public function fileable() {
        return op( $this->fileables() )->first();
    }
    
    // Schedule, Report, Calprop などのインスタンスを返す
    //
    public function getModel() {
        return op( $this->fileable() )->fileable;
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    //////////////////////////////////////////////////////////////////////////

    public function p_created_at() {
        return Carbon::parse( $this->created_at )->format( 'Y年n月j日 H:i');
    }
}



// class Fileable extends Model {
    
//     public function file() {
//         return belongsTo( File::class );
//     }
    
//     public function fileable() {
//         return $this->morphTo();
//     }
// }