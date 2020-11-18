<?php

namespace App\Models\Vacation;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

use App\Models\Vacation\Dept;
use App\Models\Vacation\ApprovalMasterList;
use App\Models\User as OriginalUser;

class User extends OriginalUser
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'email', 'password','dept_id', 'grade', 'memo', 'retired', 'date_of_retired', 'browsing'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //　以下、各ＤＢへのリレーション
    //
    public function department() {
        return $this->belongsTo("App\Models\Vacation\Dept", 'dept_id' );
        
    }

    public function getApplications() {
        return $this->hasMany( 'App\Models\Vacation\Application', 'user_id' );
    }
    
    public function vacations() {
        return $this->hasMany( 'App\Models\Vacation\Vacation', 'user_id' );
    }

    public function valid_paidleave() {
        return $this->hasMany( 'App\Models\Vacation\Vacation', 'user_id' )
                    ->where( 'action', '割当')
                    ->where( 'type', '有給休暇' )
                    ->where( 'remains_num', '>=', 1 )
                    ->where( 'done_expired', false );
        
        // $paidleaves = Vacation::where( 'user_id', $this->id )->where( 'action', '割当' )->where( 'remains_num', '>=', 1 )->get();
        // return $paidleaves;
    }

    public function valid_houly_paidleave() {
        return $this->hasMany( 'App\Models\Vacation\Vacation', 'user_id' )
                    ->where( 'action', '割当')
                    ->where( 'type', '有給休暇' )
                    ->where( 'remains_num', '>', 0 )
                    ->where( 'done_expired', false );
        
        // $paidleaves = Vacation::where( 'user_id', $this->id )->where( 'action', '割当' )->where( 'remains_num', '>=', 1 )->get();
        // return $paidleaves;
    }
    
    
    
    public function paidleaves() {
        return $this->hasMany( 'App\Models\Vacation\Vacation', 'user_id' )
                    ->where( 'action', '割当')
                    ->where( 'type', '有給休暇' );
    }

    public function approverMasterLists() {
        return $this->hasMany( 'App\Models\Vacation\ApprovalMasterList' );
    }

    //　退職しているかどうか
    //
    public function is_retired() {
        return $this->retired;
    }
    
    public function hasEmail() {
        return ! empty( $this->email );
    }
    
    public function getEmail() {
        if( $this->hasEmail() ) {
            return $this->email;
        } else {
            return null;
        }
        
    }

    // ユーザを検索
    //
    public static function getUserList( $query ) {
        //dd( $query );
        if( isset( $query['retired'])) {
            $users = User::with( 'department' )->where( 'retired', $query['retired'] );
        } else {
            $users = User::with( 'department' )->where( 'retired', "" );
        }
        if( isset( $query['code'] )) {
            $users->where( 'code', $query['code'] );
        }
        
        if( isset( $query['name'] )) {
            $users->where( 'name', 'like', "%".$query['name']."%" );
        }
        if( isset( $query['email'] )) {
            $users->where( 'email', 'like', "%".$query['email']."%" );
        }
        if( isset( $query['dept_id'] )) {
            $users->where( 'dept_id', $query['dept_id'] );
        }
        if( isset( $query['grade'] )) {
            $users->where( 'grade', $query['grade'] );
        }
        if( isset( $query['join_date']['start'])) {
            $users->where( 'join_date', '>=', $query['join_date']['start'] );
        }
        if( isset( $query['join_date']['end'])) {
            $users->where( 'join_date', '<=', $query['join_date']['end'] );
        }
        if( isset( $query['carrier'])) {
            $users->where( 'carrier', $query['carrier'] );
        }
        if( isset( $query['except_user_id'] )) {
                $users->whereNotIn( 'id', $query['except_user_id'] );
        }
        if( isset( $query['include_user_id'] )) {
                $users->whereIn( 'id', $query['include_user_id'] );
        }
        if( isset( $query['browsing'] )) {
                $users->whereIn( 'browsing', $query['browsing'] );
        }
        if( isset( $query['admin'] )) {
            if( ! is_null( $query['admin'] )) {
                $users->where( 'admin', $query['admin'] );
            }
        }

        if( isset( $query['pagination'])) {
            return $users->paginate( $query['pagination'] );
        } else {
            return $users->get();
        }
    }

    // 役職のリストを返す（フォームで利用）    
    //
    public static function getArrayForGradeSelcetForm() {

        $array = array();
        $grades = DB::table('users')->select('grade')
                                    ->where('retired', false)
                                    ->groupBy( 'grade' )
                                    ->get();

        #dd( $grades );
        foreach( $grades as $grade ) {
            $array[$grade->grade] = $grade->grade;
        }
        return $array;
    }
    
    //  部署に所属しているユーザの配列を返す（フォームで利用）
    //
    public static function getArrayUsersBlongsToDept ( ?Dept $dept ) {
        $array = array();
        
        if( isset( $dept )) {
            $users = DB::table('users')->select('id', 'name')
                                       ->where( 'dept_id', $dept->id )
                                       ->get();
            foreach( $users as $user ) {
                $array[$user->id] = $user->name;
            }
        } 
        
        return $array;
    }
    
    public static function updateDB( User $user, $data ) {
        
        $user->code     = $data['code'];
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->dept_id  = $data['dept_id'];
        $user->grade    = $data['grade'];
        $user->join_date= $data['join_date'];
        $user->carrier  = $data['carrier'];
        $user->browsing = $data['browsing'];
        // $user->admin    = $data['admin'];
        $user->memo     = $data['memo'];
        
        if( empty( $data['retired'])) {
            $user->retired  = 0;
            $user->date_of_retired  = NULL;
        } else {
            $user->retired  = 1;
            $user->date_of_retired  = $data['date_of_retired'];
        }

        $user->save();
        
        return( $user );
        
    }
    
    //  管理者かユーザか顧客か
    //
    // public function is_admin() {
        
    //     // return $this->admin;
    //     return false;
    // }  // false
    // public function is_user() {
    //     return true;
    // }       // true
    // public function is_customer() {
    //     return false;
    // }   // false
    
    //  閲覧権限の確認
    //
    public function browsing() {
        
        return $this->browsing;
    }
    

}
