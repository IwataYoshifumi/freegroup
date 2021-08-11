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

use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Traits\AccessListTrait;

class Facility extends Model {

    use AccessListTrait;
    // use SoftDeletes;
    
    protected $fillable = [
        'name', 'memo', 'type', 'not_use', 'disabled'
    ];
    // protected $hidden = [];

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function reservations() {
        return $this->hasMany( Reservation::class, 'facility_id' );
    }

    public function access_lists() {
        return $this->morphToMany( AccessList::class, 'accesslistable' );
    }

    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  検索メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function access_list() {
        return $this->access_lists->first();
    }
    
    public static function getOwner( $user_id ) {
        $access_lists = AccessList::getOwner( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Facility::class );
        // if_debug( 'getOwner', $subquery, $subquery->get() );
        return self::whereIn( 'id', $subquery )->get();
    }

    public static function getCanWrite( $user_id ) {
        $access_lists = AccessList::getCanWrite( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Facility::class );
        // if_debug( 'getCanWrite', $subquery, $subquery->get() );
        return self::whereIn( 'id', $subquery )->get();
    }
    
    public static function getCanRead( $user_id ) {
        $access_lists = AccessList::getCanRead( $user_id );

        $access_lists = $access_lists->pluck( 'id' )->toArray();
        $subquery = DB::table( 'accesslistables' )->select('accesslistable_id' )->whereIn( 'access_list_id', $access_lists )->where( 'accesslistable_type', Facility::class );
        // if_debug( 'getCanRead', $subquery, $subquery->get() );
        // 全社公開タスクも検索
        //
        
        return self::whereIn( 'id', $subquery )->orWhere( 'type', 'company-wide' )->get();
    }
    
    public static function getCategories() {
        
        $facilities = Facility::getCanRead( user_id() );
        
        if( count( $facilities )) {
            $facilities = $facilities->toQuery()->groupBy( 'category' )->select( 'category' );
            $categories = $facilities->get()->pluck('category')->toArray();
            return $categories;
        } else {
            return [];
        }        
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
    //  状態確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function is_disabled() {
        return $this->disabled == 1;
    }
    
    public function isDisabled() {
        return $this->is_disabled();
    }

    public function isNotDisabled() {
        return ! $this->is_disabled();
    }
    
    public function isPrivate() {
        return $this->type == 'private';
    }

    public function isPublic() {
        return $this->type == 'public';
    }
    
    public function isCompanyWide() {
        return $this->type == 'company-wide';
    }
    
    public function isNotCompanyWide() {
        return ! $this->isCompanyWide();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  フォーム用メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function getTypes() {
        return config( 'groupware.facility.types' );
    }
    
    public static function getDefaultPermissions() {
        return config( 'groupware.facility.permissions' );
    }
    
    public function style() {
        return "background-color:".$this->background_color."; color:".$this->text_color.";";
    }
    

}
