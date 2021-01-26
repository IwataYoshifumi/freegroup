<?php
namespace App\myHttp\GroupWare\Models\Search;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

/*
 *
 * ACLから user_id と role　の配列を返す
 * AccessListUserRole DBの元データを作成する
 *
*/
class ListOfUsersInTheAccessList {

    //  user_id と role の配列を返す
    //    
    public $access_list;
    public $owners;     // ownerのuser_idの配列
    public $writers;    // writerのuser_idの配列
    public $readers;    // readerのuser_idの配列
    public $freeBusyReaders;    // freeBusyReadersのuser_idの配列

    private $all; // order順に並んだ、user_id と role の配列
    private $users; // Access List に含まれている全て user_id の配列

    private $UserInstances; // キーは user_id, 値は Userクラスのインスタンス

    public function __construct( AccessList $access_list ) {
        
        $this->access_list = $access_list;
        $this->all = $this->make_all_list_first();
        $this->create_user_lists_by_roles();
        // if_debug( $this );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  プライベートなメソッド
    //
    ///////////////////////////////////////////////////////////////////////////////////////////////
    
    //　とりあえず、order 順にユーザとロールのリストをつくる
    //
    private function make_all_list_first() {
        
        $access_list = $this->access_list;
        $select_users  = '"users" as types,  acls.order as order_index, users.id as user_id, users.name as user_name, acls.role as user_role';
        $select_depts  = '"depts" as types,  acls.order as order_index, users.id as user_id, users.name as user_name, acls.role as user_role';
        $select_groups = '"groups" as types, acls.order as order_index, users.id as user_id, users.name as user_name, acls.role as user_role';

        $users = DB::table( 'users' )
                    ->selectRaw( $select_users )
                    ->join( 'acls', function( $join ) {
                        $join->on( 'users.id', '=', 'acls.aclable_id' )->where( 'acls.aclable_type', User::class ); })
                    ->where( 'acls.access_list_id', $access_list->id )
                    ->where( 'users.retired', 0 );

        $depts = DB::table( 'users' )
                    ->selectRaw( $select_depts )
                    ->join( 'depts', 'users.dept_id', '=', 'depts.id' )
                    ->join( 'acls', function( $join ) {
                        $join->on( 'depts.id', '=', 'acls.aclable_id' )->where( 'acls.aclable_type', Dept::class ); })
                    ->where( 'acls.access_list_id', $access_list->id )
                    ->where( 'users.retired', 0 );

        $groups= DB::table( 'users' )
                    ->selectRaw( $select_groups )
                    ->join( 'groupables', function( $join ) { 
                        $join->on( 'users.id', '=', 'groupables.groupable_id' )->where( 'groupables.groupable_type', User::class ); })
                    ->join( 'groups', 'groups.id', '=', 'groupables.group_id' )
                    ->join( 'acls', function( $join ) {
                        $join->on( 'groups.id', '=', 'acls.aclable_id' )->where( 'acls.aclable_type', Group::class ); })
                    ->where( 'acls.access_list_id', $access_list->id )
                    ->where( 'users.retired', 0 );

        // if_debug( $users, $depts, $groups );
        $all = $users->union( $depts )->union( $groups )->orderBy( 'order_index' )->get();

        return $all;
    }

    //  権限別( owner, writer, reader, freeBusyReader )にユーザを分ける
    // 
    private function create_user_lists_by_roles() {

        $users   = [];
        $owners  = [];
        $writers = [];
        $readers = [];
        $freeBusyReaders = [];
        
        foreach( $this->all as $i => $row ) {
            // if_debug( $row );

            if( in_array( $row->user_id, $users  )) { continue; }

            $order     = $row->order_index;
            $user_id   = $row->user_id;
            $user_role = $row->user_role;
            $user_name = $row->user_name;
            array_push( $users, $user_id );

            #if_debug( "$i, $order, $r, $user_id, $user_name" );

            $r = $user_role."s";
            array_push( $$r, $user_id );
        }

        $this->users = $users;
        $this->owners = $owners;
        $this->writers = $writers;
        $this->readers = $readers;
        $this->freeBusyReaders = $freeBusyReaders;

        return true;        
    }
    
    // Userクラスのインスタンスを取得する
    //
    private function init_user_instances() {
        $users = [];
        foreach( User::with( ['dept'] )->find( $this->users ) as $i => $user ) {
            $users[$user->id] = $user;
        }
        $this->UserInstances = $users;
        return $users;
    }

    private function getUserInstances( $users ) {
        
        if( ! $this->UserInstances ) { $this->init_user_instances(); }
        
        $array = [];
        foreach( $users as $i => $user_id ) {
            $array[$user_id] = $this->UserInstances[$user_id];
        }
        return $array;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  パブリックなメソッド
    //
    ///////////////////////////////////////////////////////////////////////////////////////////////
    public function getUsers() {
        return $this->getIncludeUsers();
    }
    
    public function getIncludeUsers() {
        return $this->users;
    }
    
    public function getOwners() {
        return $this->getUserInstances( $this->owners );
    }
    
    public function getWriters() {
        return $this->getUserInstances( $this->writers );
    }
    
    public function getReaders() {
        return $this->getUserInstances( $this->readers );
    }
    
    public function getFreeBusyReaders() {
        return $this->getUserInstances( $this->freeBusyReaders );
    }
    
    // ownerかチェック
    public function isOwner( $user_id ) {
        return in_array( $user_id, $this->owners );
    }
    
    // writerかチェック
    public function isWriter( $user_id ) {
        return in_array( $user_id, $this->writers );
    }
    
    // readerかチェック
    public function isReader( $user_id ) {
        return in_array( $user_id, $this->readers );
    }

    // freeBusyWiterかチェック
    public function isFreeBusyReader( AccessList $access_list ) {
        return in_array( $user_id, $this->owners );
    }

    public function canAccess( $user_id ) {
        return in_array( $user_id, $this->users );
    }

    // 設定権限があるかチェック( ownerのみ true )
    public function canConfigurate( $user_id ) {
        return $this->isOwner( $user_id );
    }

    // 編集権限があるかチェック( owner, writer はtrue );
    public function canWrite( $user_id ) {
        return $this->isOwner( $user_id ) or 
               $this->isWriter( $user_id ); 
    }
    
    // 閲覧権限があるかチェック( owner, writer, reader はtrue );
    public function canRead( $user_id ) {
        return $this->isOwner( $user_id ) or 
               $this->isWriter( $user_id ) or
               $this->isReader( $user_id ); 
    }

    // 制限付き閲覧権限があるかチェック( owner, writer, reader はtrue );
    public function canFreeBusyReader( $user_id ) {
        return $this->isOwner( $user_id ) or 
               $this->isWriter( $user_id ) or
               $this->isReader( $user_id ) or
               $this->isFreeBusyReader( $user_id ); 
    }
    
    // ユーザのアクセス権限を返す( owner / writer / reader ... 権限がなければ null を返す)
    public function getUserRole( $user_id ) {
        if( in_array( $user_id, $this->owners )) { 
            return 'owner'; 
        } elseif( in_array( $user_id, $this->writers )) {
            return 'writer';
        } elseif( in_array( $user_id, $this->readers )) {
            return 'reader';
        } elseif( in_array( $user_id, $this->freeBusyReaders )) {
            return 'freeBusyReader';
        } else {
            return null;
        }
    }
    

    
}

