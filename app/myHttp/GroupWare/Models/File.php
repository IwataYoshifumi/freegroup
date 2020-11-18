<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ScheduleType;
use App\myHttp\GroupWare\Models\Report;

class File extends Model {
    
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

    //  紐づけられたものスケジュール・日報
    //
    public function reports() {
        return $this->morphedByMany( Report::class, 'fileable' );
    }
    
    public function schedules() {
        return $this->morphedByMany( Schedule::class, 'fileable' );
    }
    
    public function schedule_types() {
        return $this->morphedByMany( ScheduleType::class, 'fileable' );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    //////////////////////////////////////////////////////////////////////////

    public function p_created_at() {
        return Carbon::parse( $this->created_at )->format( 'Y年n月j日 H:i');
        
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //  検索
    //
    //////////////////////////////////////////////////////////////////////////
    
    public static function search( $find, $pagination = 20 ) {
    
        $files = new File;

        if( ! empty( $find['start_date'] )) {
            $files = $files->where( 'created_at', '>=', $find['start_date']." 00:00" );
        }
        if( ! empty( $find['end_date'])) {
            $files = $files->where( 'created_at', '<=', $find['end_date']." 23:59:59" );
        }
        if( ! empty( $find['file_name'])) {
            $files = $files->where( 'file_name', 'like', '%'.$find['file_name'].'%' );
        }
        if( ! empty( $find['users']) and is_array( $find['users'])) {
            $files = $files->wherein( 'user_id', $find['users'] );
        }

        // 添付のあり・なし
        //
        if( $find['attached'] == 1 ) {
            // 添付あり
            $files = $files->where( function( $query ){
                return $query->has( 'schedules' )->orhas( 'reports' )->orhas( 'schedule_types' );
            });

        } elseif( $find['attached'] == -1 ) {
            // 添付なし
            $files = $files->where( function( $query ) {
                return $query->doesntHave( 'schedules' )->doesntHave( 'reports' )->doesntHave( 'schedule_types' );
            });
        }
        
        
        // dump( $files );
        $files = $files->with( 'user', 'schedules', 'reports', 'schedule_types' )->paginate( $pagination );
        
        return $files;
        
    }

}
