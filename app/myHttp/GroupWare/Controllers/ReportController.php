<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection ;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Controllers\Controller;

use App\myHttp\GroupWare\Requests\ReportRequest;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

class ReportController extends Controller {

    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {
        
        if( $request->from_menu ) {
            $find['users'] = [ auth( 'user' )->id() ];

            $find['start_date']  = Carbon::parse('today')->subMonth()->format('Y-m-d');      
            $find['end_date']    = Carbon::parse('today')->format('Y-m-d');
            $find['search_mode'] = 1;
        } else {
            $find = ( isset( $request->find )) ? $request->find : [] ;
            $find['users']     = ( isset( $request->users )) ? $request->users : [];
            $find['customers'] = ( isset( $request->customers )) ? $request->customers : [];
        }
        // dump( $find );
        //　検索
        //
        $reports = Report::search( $find, $request->search_mode );
        
        BackButton::setHere( $request );
        return view( 'groupware.report.index' )->with( 'reports', $reports )
                                               ->with( 'request', $request )
                                               ->with( 'find', $find );
    }
    
    public function csv( Request $request ) {
        // dump( $request->all() );
        $reports = Report::search( $request->find, $request->search_mode );
        // dump( $reports );
        $values = [];
        foreach( $reports as $i => $r ) {
            $start_time  = Carbon::parse( $r->start_time );
            $end_time    = Carbon::parse( $r->end_time );
            $period      = $start_time->diffInMinutes( $end_time );
            $period_hour = round( $period / 60, 2 );
    
            $users = "";        
            if( count( $r->users )) {
                // dump( $r->users );
                foreach( $r->users as $i => $user ) {
                    if( $i == 0 ) {
                        $users = $user->name;
                    } else {
                        $users .= ",".$user->name;
                    }
                }
            }

            $customers = "";        
            if( count( $r->customers )) {
                // dump( $r->customers );
                foreach( $r->customers as $i => $customer ) {
                    if( $i == 0 ) {
                        $customers = $customer->name;
                    } else {
                        $customers .= ",".$customer->name;
                    }
                }
            }

            $v = [  $r->user->name, 
                    $r->name, 
                    $r->place, 
                    $r->start_time->format( 'Y-n-j'), 
                    $r->start_time->format( 'H:m' ),
                    $r->end_time->format( 'Y-n-j H:m'), 
                    $r->end_time->format( 'H:m'), 
                    $period,
                    $period_hour,
                    $users,
                    $customers,
                    $r->memo,
                    ];
            array_push( $values, $v );
            
        }
        $options['lists'] = $values;
        // $options['column_name'] = [ '作成者', '件名', '場所', '開始日時', '終了日時', '所要時間（分）', '関連社員', '関連顧客', '報告内容' ];
        $options['column_name'] = [ '作成者', '件名', '場所', '開始日', '開始時刻', '終了日', '終了時刻', '所要時間（分）','所要時間（時間）', '関連社員', '関連顧客', '報告内容' ];
        // dd( $options );
        return OutputCSV::input_array( $options );
        
    }
    
    public function create( Request $request ) {
        
        $this->authorize( 'create', Report::class );
        // dump( session()->all() );
        // dump( $request->all() );
        
        //　初期値設定
        //
        $report = new Report;
        $report->user_id = auth('user')->id();

        if( optional( $request )->schedule_id ) {
            
            $schedule = Schedule::find( $request->schedule_id );
            // dd( $schedule );
            $report->schedules    = [ $schedule ];
            // $report->schedule_id  = $request->schedule_id;
            $report->name         = $schedule->name;
            $report->place        = $schedule->place;
            $report->start_time   = $schedule->o_start_time(); 
            $report->end_time     = $schedule->o_end_time();
            $report->users        = $schedule->users;
            $report->customers    = $schedule->customers;
        }
        // dump( $report );
        BackButton::stackHere( request() );
        return view( 'groupware.report.input' )->with( 'report', $report );
        
    }

    public function store( ReportRequest $request ) {
        
        $this->authorize( 'create', Report::class );
        
        $report = DB::transaction( function() use( $request ) {
            $report = new Report;

            $report->user_id   = auth('user')->id();
            $report->name      = $request->name;
            $report->place     = $request->place;
            $report->start_time = $request->start_time;
            $report->end_time   = $request->end_time;
            $report->memo      = $request->memo;
            
            $report->save();
            
            $report->customers()->sync( $request->customers );
            $report->users()->sync( $request->users );
            $report->schedules()->sync( $request->schedules );
            
            $files = [];
            foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
                // dump( "aaa", $i, $file );
                $path = $file->store('');
                $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->id() ];
                $f = MyFile::create( $value );
                $files[$i] = $f->id;
            }
            // dd( $files );
            $report->files()->sync( $files );
            
            return $report;
        });

        // dump( $request->all(), $report );
        // return view( 'groupware.report.show' )->with( 'report', $report );

        session()->regenerateToken();
        session()->flash( 'flash_message', "日報「". $request->title. "」を追加しました。" );
        BackButton::removePreviousSession();
        return redirect()->route( 'groupware.report.show', [ 'report' =>  $report ]);
        
    }
    
    public function show( Report $report ) {
        // dump( $report->schedules );

        BackButton::stackHere( request() );
        return view( 'groupware.report.show' )->with( 'report', $report );
    }
    
    public function detail() {
        
    }

    public function edit( Report $report ) {
        
        $this->authorize( 'update', [ $report, auth('user')->user() ]);
        
        BackButton::stackHere( request() );
        return view( 'groupware.report.input' )->with( 'report', $report );
    }
    public function update( Report $report, ReportRequest $request ) {
        
        $this->authorize( 'update', [ $report, auth('user')->user() ]);
        
        // dd( $request );
        $report = DB::transaction( function() use( $request, $report ) {

            // $report->user_id  = $request->user_id
            $report->name        = $request->name;
            $report->place       = $request->place;
            $report->start_time  = $request->start_time;
            $report->end_time    = $request->end_time;
            $report->memo        = $request->memo;
            $report->save();

            $report->customers()->sync( $request->customers );
            $report->users()->sync( $request->users );
            $report->schedules()->sync( $request->schedules );
            
            //　アップロードファイル
            //
            $files = ( ! empty( $request->attached_files )) ? $request->attached_files : [] ;
            // dd( $request->file( 'upload_files' ));
            foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
                dump( "aaa", $i, $file );
                $path = $file->store('');
                $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->user()->id ];
                $f = MyFile::create( $value );
                // $files[$i] = $f->id;
                array_push( $files, $f->id );
            }
            // dd( $files );
            $report->files()->sync( $files );
            
            
            return $report;
        });
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        return redirect()->route( 'groupware.report.show', [ 'report' =>  $report ]);
        
    }
    
    public function delete( Report $report ) {
        $this->authorize( 'delete', [ $report, auth('user')->user() ]);

        return view( 'groupware.report.delete' )->with( 'report' , $report );
    }
    public function deleted( Report $report ) {
        
        $this->authorize( 'delete', [ $report, auth('user')->user() ]);
        
        DB::transaction( function() use( $report ) {
            $report->customers()->detach();
            $report->users()->detach();
            $report->files()->detach();
            $report->schedules()->detach();
            $report->delete();
        });
        
        session()->regenerateToken();
        return view( 'groupware.report.delete' )->with( 'report' , $report );
    }
    
}
