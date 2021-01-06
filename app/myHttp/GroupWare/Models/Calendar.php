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

use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Traits\AccessListTrait;


class Calendar extends Model {

    use AccessListTrait;
    // use SoftDeletes;
    
    protected $fillable = [
        'name', 'memo', 'type', 'not_use', 'disabled'
    ];
    // protected $hidden = [];

    const CALENDAR_TYPES = [ 'public', 'private', 'company-wide' ];

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function schedules() {
        return $this->hasMany( Schedule::class );
    }

    public function access_lists() {
        return $this->morphToMany( AccessList::class, 'accesslistable' );
    }

    public function calprops() {
        return $this->hasMany( CalProp::class );
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  検索メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function calprop( $user = null ) {
        
        $user    = ( empty( $user )) ? user_id() : $user ;
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        // dump( $this->calendar()->calprop->where( 'user_id', $user_id )->first() );
        return $this->calprops()->where( 'user_id', $user_id )->first();
    }

    public function access_list() {
        return $this->access_lists->first();
        // return $this->access_lists()->first();
    }
    
    public static function getOwner( $user_id ) {
        $access_lists = AccessList::getOwner( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Calendar::class );
        // dump( 'getOwner', $subquery, $subquery->get() );
        return self::whereIn( 'id', $subquery )->get();
    }

    public static function getCanWrite( $user_id ) {
        $access_lists = AccessList::getCanWrite( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Calendar::class );
        // dump( 'getCanWrite', $subquery, $subquery->get() );
        return self::whereIn( 'id', $subquery )->get();
    }
    
    public static function getCanRead( $user_id ) {
        $access_lists = AccessList::getCanRead( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Calendar::class );
        // dump( 'getCanRead', $subquery, $subquery->get() );
        return self::whereIn( 'id', $subquery )->get();
    }
    

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  権限確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////    
    public function isOwner( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isOwner( $user_id );
    }
    public function isWriter( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isWriter( $user_id );
    }
    public function isReader( $user ) {
        if( $this->type == 'public' or $this->type == 'company-wide' ) { return true; }
        
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isReader( $user_id );
    }
    public function canWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->canWrite( $user_id );
    }
    public function canRead( $user ) {
        if( $this->type == 'public' or $this->type == 'company-wide' ) { return true; }

        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->canRead( $user_id );
    }
    

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  フォーム用メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function getTypeByUserRole( $user_id ) {
        
        
    }
    
    public static function getTypes() {
        // return self::CALENDAR_TYPES;
        return config( 'groupware.calendar.types' );
    }


}
