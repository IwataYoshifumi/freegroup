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
use Exception;
use Illuminate\Auth\Access\Response;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Requests\ReportPropRequest;
use App\myHttp\GroupWare\Models\Actions\ReportPropAction;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;


class ReportPropController extends Controller {
    
    public function index( Request $request ) {
        $controller = new CalendarController;
        return $controller->index( $request );
    }
    
    public function show( ReportProp $report_prop ) {

        $this->authorize( 'view', $report_prop );
        
        if_debug( $report_prop->getAttributes() );

        BackButton::stackHere( request() );
        return view( 'groupware.report_prop.show' )->with( 'report_prop', $report_prop );
    }
    
    public function create() {
        $this->authorize( 'create', ReportProp::class );
    }
    
    public function store( ReportPropRequest $request ) {
        $this->authorize( 'create', ReportProp::class );
    }
    
    public function edit( ReportProp $report_prop ) {

        if_debug( old() );
        $this->authorize( 'update', $report_prop );
        
        BackButton::stackHere( request() );
        return view( 'groupware.report_prop.input' )->with( 'report_prop', $report_prop );
    }
    
    public function update( ReportProp $report_prop, ReportPropRequest $request ) {

        $this->authorize( 'update', $report_prop );

        $old_report_prop = clone $report_prop;
        $report_prop = ReportPropAction::updates( $report_prop, $request );        

        // Google同期処理関係のイベント発行
        //
        if( $old_report_prop->google_sync_on != $report_prop->google_sync_on ) {
            
            
            session()->flash( 'error_message', 'Googleカレンダー関連の設定が変更されたので、一旦同期が解除になりました');
        }

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー表示設定・Google同期設定を変更しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.report_prop.show', [ 'report_prop' => $report_prop->id ]);

        // return view( 'groupware.report_prop.show' )->with( 'report_prop', $report_prop );
    }
    
    public function delete() {
        return die( __METHOD__ );
    }
    
}
