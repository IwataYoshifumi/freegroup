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

use App\myHttp\GroupWare\Requests\ReportListRequest;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReporPropt;

use App\myHttp\GroupWare\Models\Actions\ReportListAction;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;


class ReportListController extends Controller {
    
    public function index( Request $request ) {
        
        $this->authorize( ReportList::class );
        
        // $report_lists = SearchReportList::search( $find );
        $report_lists = ReportList::all();
        
        // $report_lists = ReportList::with( [
        //         'access_lists', 
        //         'calprops' => function( $query ) { $query->where( 'user_id', user_id() ); } 
        //     ] )->get();
        // $report_lists = ReportList::all();
        
        BackButton::setHere( $request );
        return view( 'groupware.report_list.index' )->with( 'report_lists', $report_lists )
                                                    ->with( 'request',      $request      );
    }
    
    public function show( ReportList $report_list ) {
        
        $this->authorize( 'view', $report_list );
        
        $access_list = $report_list->access_list();
        
        BackButton::stackHere( request() );
        return view( 'groupware.report_list.show' )->with( 'report_list', $report_list );
    }
    
    public function create() {

        $report_list = new ReportList;
        $access_list = new AccessList;
        
        BackButton::stackHere( request() );
        return view( 'groupware.report_list.input' )->with( 'report_list', $report_list )
                                                    ->with( 'access_list', $access_list );
    }
    
    public function store( ReportListRequest $request ) {

        $report_list = ReportListAction::creates( $request );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "日報リスト「". $report_list->name . "」を作成しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.report_list.show', [ 'report_list' => $report_list->id ]);
    }
    
    public function edit( ReportList $report_list ) {
        
        $this->authorize( 'update', $report_list );
            
        $access_list = $report_list->access_list();
        
        BackButton::stackHere( request() );
        return view( 'groupware.report_list.input' )->with( 'report_list', $report_list )
                                                    ->with( 'access_list', $access_list );
    }
    
    public function update( ReportList $report_list, ReportListRequest $request ) {

        $this->authorize( 'update', $report_list );
        
        $old_report_list = clone $report_list;
        
        $report_list = ReportListAction::updates( $report_list, $request );

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "日報リスト「". $report_list->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.report_list.show', [ 'report_list' => $report_list->id ]);
    }
    
    public function delete( ReportList $report_list ) {
        
        $this->authorize( 'delete', $report_list );

        BackButton::stackHere( request() );
        return view( 'groupware.report_list.delete' )->with( 'report_list', $report_list );

    }
    
    public function deleted( ReportList $report_list, ReportListRequest $request ) {
        
        $this->authorize( 'delete', $report_list );

        ReportListAction::deletes( $report_list );

        BackButton::removePreviousSession();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "日報「". $report_list->name . "」と関連データ等は完全に削除されました" );

        // return self::show( $report_list );
        return self::index( $request );
        // return redirect()->route( 'groupware.report_list.list' );
    }
    
}
