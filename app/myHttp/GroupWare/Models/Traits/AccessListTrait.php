<?php

namespace App\myHttp\GroupWare\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Exception;

use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
// use App\myHttp\GroupWare\Models\Search\CheckAccessList;
use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

trait AccessListTrait {
    
    public function isOwner( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isOwner( $user_id );

        // return CheckAccessList::isOwner( $user_id, $this->access_list()->id );
    }
    
    public function isWriter( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isWriter( $user_id );
        
        // return CheckAccessList::isWriter( $user_id, $this->access_list()->id );
    }
    
    public function isReader( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->isReader( $user_id );

        // return CheckAccessList::isReader( $user_id, $this->access_list()->id );
    }
    
    public function canWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->canWrite( $user_id );
        // return CheckAccessList::canWrite( $user_id, $this->access_list()->id );
    }
    
    public function canNotWrite( $user ) {
        return ! $this->canWrite( $user );
    }
    
    public function canRead( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->access_list()->canRead( $user_id );
        // return CheckAccessList::canRead( $user_id, $this->access_list()->id );
    }

    public function canNotRead( $user ) {
        return ! $this->canRead( $user );
    }

    
    public static function whereOwner( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;

        $subquery = AccessList::whereOwner( $user_id )->select('id');
        $builder =  self::whereHas( 'access_lists', function( $query ) use ( $subquery ) {
            $query->whereIn( 'access_list_id', $subquery );
        } );
        return $builder;
    }
    
    public static function whereWriter( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;

        $subquery = AccessList::whereWriter( $user_id )->select('id');
        $builder =  self::whereHas( 'access_lists', function( $query ) use ( $subquery ) {
            $query->whereIn( 'access_list_id', $subquery );
        } );

        return $builder;
    }
    
    public static function whereReader( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;

        $subquery = AccessList::whereReader( $user_id )->select('id');
        $builder =  self::whereHas( 'access_lists', function( $query ) use ( $subquery ) {
            $query->whereIn( 'access_list_id', $subquery );
        } );

        return $builder;
    }
    
    public static function whereCanWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $subquery = AccessList::whereCanWrite( $user_id )->select('id');
        
        $builder =  self::whereHas( 'access_lists', function( $query ) use ( $subquery ) {
            $query->whereIn( 'access_list_id', $subquery );
        } );

        return $builder;
    }
    
    public static function whereCanRead( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        
        $subquery = AccessList::whereCanRead( $user_id )->select('id');
        $builder =  self::whereHas( 'access_lists', function( $query ) use ( $subquery ) {
            $query->whereIn( 'access_list_id', $subquery );
        } );
        
        return $builder;
    }
    
    
    
}