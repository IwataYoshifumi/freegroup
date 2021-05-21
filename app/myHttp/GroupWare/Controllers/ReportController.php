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

use App\myHttp\GroupWare\Controllers\Search\SearchReport;


class ReportController extends Controller {

    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {
        
        if_debug( $request->all() );
        
        if( $request->from_menu ) {
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->search_condition = 'users';
            $request->search_date_condition = 'report_date';
            $request->display_axis = 'users';
        } elseif( $request->report_list_id ) {
            if( ! isset( $request->sorts      )) { $request->sorts = [ 'created_at' ]; }
            if( ! isset( $request->pagination )) { $request->pagination = 3; }
            
        }
            
        //　検索
        //
        $returns = SearchReport::search( $request );
        
        BackButton::setHere( $request );
        return view( 'groupware.report.index2' )->with( 'returns', $returns )
                                               ->with( 'request', $request );
    }
    
    public function create( Request $request ) {
        
        $this->authorize( 'create', Report::class );
        // if_debug( session()->all() );
        // if_debug( $request->all() );
        
        //　初期値設定
        //
        if( is_null( $request->report )) {
            //　新規日報作成
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
            $input    = new DateTimeInput( );

        } else {
            //　複製
            //
            $report = Report::find( $request->report );
            $report->user_id = user_id();
            
            $input = new DateTimeInput( $report );
            
        }
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        
        
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
        $this->authorize( 'view', $report );

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
    
    public function csv( Request $request ) {
        
        $returns = SearchReport::search( $request );
        
        $reports = $returns['reports'];
        // dd( $reports );
        $values['column_name'] = [ '作成者', '件名', '場所', '開始日', '開始時刻', '終了日', '終了時刻', '所要時間（分）','所要時間（時間）', '関連社員', '関連顧客', '報告内容' ];
        $values['lists'] = [];
        foreach( $reports as $report ) {
            $attendees = '';
            foreach( $report->users as $attendee ) {
                if( empty( $attendees )) { 
                    $attendees .= $attendee->name;
                } else { 
                    $attendees .= "," . $attendee->name;                    
                }
            }
            // dump( $attendees );
            
            $value = [ 
                op( $report->user )->name,
                $report->name,
                $report->place,
                $report->p_dateTime(),
                $report->memo,
                $attendees
                ];
            array_push( $values['lists'], $value );            
            
        }
        // return response()->json( $values );
        return OutputCSV::input_array( $values );
        
        
    }

    public function copy( Report $report ) {
        
        $this->authorize( 'create', Report::class );
        $url = route( 'groupware.report.create' );
        $url .= '?report=' . $report->id;
        return redirect( $url );
    
    }


}
