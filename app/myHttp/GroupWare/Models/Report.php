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

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

class Report extends Model {

    // use SoftDeletes;
    
    protected $fillable = [
        'user_id', 'schedule_id',
        'title', 'place', 'start_time', 'end_time', 'memo', 
    ];

    // protected $hidden = [];

    protected $dates = [ 'start_time', 'end_time' ];
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    //　作成者
    public function user() {
        return $this->belongsTo( User::class );
    }
    
    public function users() {
        // return $this->belongsToMany( User::class, 'r_reports_users', 'report_id', 'user_id' );
        return $this->morphedByMany( User::class, 'reportable' );
    }

    public function customers() {
        // return $this->belongsToMany( Customer::class, 'r_reports_customers', 'report_id', 'customer_id' );
        return $this->morphedByMany( Customer::class, 'reportable' );
    }
    
    public function schedules() {
        return $this->morphedByMany( Schedule::class, 'reportable' );
    }

    public function files() {
        // return $this->belongsToMany( MyFile::class, 'r_files_reports', 'report_id', 'file_id' );
        return $this->morphToMany( MyFile::class, 'fileable' );
    }

    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function p_start_date() {
        return Carbon::parse( $this->start_time )->format( 'Y-n-j' );
    }
    
    public function print_time() {
        $start_time = $this->start_time;
        $end_time   = $this->end_time;
        
        if( $start_time->diffInDays( $end_time ) == 0 ) {
            if( $start_time->diffInMinutes( $end_time ) == 0 ) {
                $print = $start_time->format( 'Y年n月j日 H:i' );
            } else {
                $print = $start_time->format( 'Y年n月j日' )."  ".$start_time->format('H:i')." ～ ".$end_time->format('H:i');
            }
            
        } else {
            $print = $start_time->format('Y-m-d H:i')."～".$end_time->format('Y-m-d H:i');
        }
        return $print;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //    
    //　出力用関数（ブラウザの入力フォーム(datetime-local)に値が渡せる日付フォーマットを出力
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function o_start_time() {
        $time = new Carbon( $this->start_time );
        return $time->format( 'Y-m-d\TH:i');
    }

    public function o_end_time() {
        $time = new Carbon( $this->end_time );
        return $time->format( 'Y-m-d\TH:i');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //　検索する
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    //　作成した人、関連者をまとめて検索
    //
    //  $search_mode = 0  スケジュール作成者のみを検索
    //  $search_mode = 1  スケジュール関連者のみを検索
    //  $search_mode = 2  スケジュール作成者・関連者を検索（関連者は重複削除）
    
    static public function search( $find, $search_mode = null, $sort = null, $asc_desc = null ) {

        if( empty( optional( $find )['start_date']) or empty( optional( $find )['end_date'])) { return []; }

        $start_date = Carbon::parse( $find['start_date'] )->format( 'Y-m-d 00:00:00' );
        $end_date   = Carbon::parse( $find['end_date']   )->format( 'Y-m-d 23:59:59' );
        
        $reports = Report::where( function( $sub_query ) use ( $start_date, $end_date ) {
                                $sub_query->where( function( $query ) use ( $start_date, $end_date ) {
                                            $query->where( 'start_time', '>=', $start_date )
                                                  ->where( 'start_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                                            $query->where( 'end_time', '>=', $start_date )
                                                  ->where( 'end_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date) {
                                            $query->where( 'start_time', '<', $start_date )
                                                  ->where( 'end_time',   '>', $end_date   );
                                            });
                    });

        $reports2 = clone $reports;

        //  顧客検索
        //
        if( ! empty( $find['customers'])) {
            $sub_query = DB::table( 'customers' )->select('id')->whereIn( 'id', $find['customers']);
            $reports = $reports->whereHas( 'customers', function( $query ) use ( $sub_query ) {
                                        // $query->whereIn( 'customer_id', $sub_query );
                                        $query->whereIn( 'reportable_id', $sub_query );

                                        
                            });
            $reports2 = $reports2->whereHas( 'customers', function( $query ) use ( $sub_query ) {
                                        // $query->whereIn( 'customer_id', $sub_query );
                                        $query->whereIn( 'reportable_id', $sub_query );
                            });

            
        }

        //　部署検索
        //
        if( ! empty( $find['dept_id'] )) {
            // dump( 'dept_id', $request->dept_id );
            
            $sub_query = DB::table( 'users' )->select( 'id' )->where( 'dept_id', $find['dept_id'] );

            $reports  = $reports ->whereIn( 'user_id', $sub_query );
            $reports2 = $reports2->whereHas( 'users', function( $query ) use ( $sub_query ) {
                    // $query->whereIn( 'user_id', $sub_query );
                    $query->whereIn( 'reportable_id', $sub_query );
                });
        }
    
        //　社員検索
        //
        if( array_key_exists( 'users', $find ) and count( $find['users'] )) {
            $reports  = $reports ->whereIn( 'user_id', $find['users']);
            
            $reports2 = $reports2->whereHas( 'users', function( $query ) use ( $find ) {
                                // $query->whereIn( 'user_id', $find['users'] );
                                $query->whereIn( 'reportable_id', $find['users'] );
                            });
            
        }

        //　検索実行
        //
        // dump( $reports );
        if( empty( $search_mode )) {
            //
            //　作成者ベースで検索
            //
            $returns = $reports->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, memo, user_id' )
                                 ->with( 'user' )->orderBy( 'start_time' )->get();
        } elseif( $search_mode == 1 ) {
            //
            //  関連者ベースで検索
            //
            $returns = $reports2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, memo, user_id' )
                                  ->with( 'users', 'user' )->orderBy( 'start_time' )->get();    
        } elseif( $search_mode == 2 ) {
            //
            //  作成者・関連者両方で検索（関連者は重複削除）
            //
            $sub_query = clone $reports;

            $reports2= $reports2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, memo, user_id' )
                                    ->whereNotIn( 'id', $sub_query->select( 'id' ) )
                                    ->with( 'users', 'user' );

            $reports = $reports ->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, memo, user_id' )
                                    ->with( 'user' );
                                    
            $returns = $reports->union( $reports2 )->orderBy( 'start_time' )->get();        

        }
        // dump( $returns );

        return $returns;
    }
    


}
