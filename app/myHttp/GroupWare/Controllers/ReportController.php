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



use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\ReportAction;
use App\myHttp\GroupWare\Requests\ReportRequest;
use App\myHttp\GroupWare\Requests\SubRequests\ComfirmDeletionRequest;

use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;
use App\myHttp\GroupWare\Controllers\SubClass\DateTimeInput;

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
        // if_debug( $find );
        //　検索
        //
        // $reports = Report::search( $find, $request->search_mode );
        $reports = Report::all();
        
        BackButton::setHere( $request );
        return view( 'groupware.report.index' )->with( 'reports', $reports )
                                               ->with( 'request', $request )
                                               ->with( 'find', $find );
    }
    
    public function csv( Request $request ) {
        // if_debug( $request->all() );
        $reports = Report::search( $request->find, $request->search_mode );
        // if_debug( $reports );
        $values = [];
        foreach( $reports as $i => $r ) {
            $start_time  = Carbon::parse( $r->start_time );
            $end_time    = Carbon::parse( $r->end_time );
            $period      = $start_time->diffInMinutes( $end_time );
            $period_hour = round( $period / 60, 2 );
    
            $users = "";        
            if( count( $r->users )) {
                // if_debug( $r->users );
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
                // if_debug( $r->customers );
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
        // if_debug( session()->all() );
        // if_debug( $request->all() );
        
        //　初期値設定
        //
        $report = new Report;
        $report->user_id = auth('user')->id();
        
        if( optional( $request )->schedule_id ) {
        
            $schedule = Schedule::where( 'id', $request->schedule_id )->first();
            $schedule->load( 'users', 'customers');
            $report->schedules[]    = $schedule;
            $report->name           = $schedule->name;
            $report->place          = $schedule->name;
            $report->start_date     = $schedule->start_date;
            $report->end_date       = $schedule->end_date;
            $report->start          = $schedule->start;
            $report->end            = $schedule->end;
            $report->all_date       = $schedule->all_date;
            $report->users        = $schedule->users;
            $report->customers    = $schedule->customers;
        }
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        
        $input    = new DateTimeInput( );
        
        // if_debug( $report );
        BackButton::stackHere( request() );
        return view( 'groupware.report.input' )->with( 'report', $report )
                                               ->with( 'input',  $input  )
                                               ->with( 'component_input_files', $component_input_files );
        
    }

    public function store( ReportRequest $request ) {
        
        $this->authorize( 'create', Report::class );
        
        $report = ReportAction::creates( $request );


        session()->regenerateToken();
        session()->flash( 'flash_message', "日報「". $request->title. "」を追加しました。" );
        BackButton::removePreviousSession();
        
        // return view( 'groupware.report.show' )->with( 'report', $report  );
        return redirect()->route( 'groupware.report.show', [ 'report' =>  $report ]);
        
    }
    
    public function show( Report $report ) {
        // if_debug( $report->schedules );

        BackButton::stackHere( request() );
        return view( 'groupware.report.show' )->with( 'report', $report );
    }
    
    public function edit( Report $report ) {
        
        $this->authorize( 'update', [ $report, auth('user')->user() ]);
        
        $report->load( 'users','users.dept', 'customers', 'files' );
        $component_input_files = new ComponentInputFilesClass( 'attach_files', $report->files  );
        $input    = new DateTimeInput( $report );
        
        BackButton::stackHere( request() );
        return view( 'groupware.report.input' )->with( 'report', $report )
                                               ->with( 'input',  $input   )
                                               ->with( 'component_input_files', $component_input_files );
    }

    public function update( Report $report, ReportRequest $request ) {
        
        $this->authorize( 'update', [ $report, auth('user')->user() ]);
        
        $report = ReportAction::updates( $report, $request ); 
        
        session()->regenerateToken();
        BackButton::removePreviousSession();

        session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        return redirect()->route( 'groupware.report.show', [ 'report' =>  $report ]);
        
    }
    
    public function delete( Report $report ) {
        $this->authorize( 'delete', [ $report, auth('user')->user() ]);

        return view( 'groupware.report.delete' )->with( 'report' , $report );
    }
    public function deleted( Report $report, ComfirmDeletionRequest $request ) {
        
        $this->authorize( 'delete', [ $report, auth('user')->user() ]);
        
        ReportAction::deletes( $report );

        session()->regenerateToken();
        return view( 'groupware.report.delete' )->with( 'report' , $report );
    }
    
}
