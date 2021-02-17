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
use App\Http\Helpers\MyGoogleCalendarClient;

use App\myHttp\GroupWare\Requests\CalPropRequest;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\CalendarController;

use App\myHttp\GroupWare\Models\Actions\CalPropAction;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\FirstScheduledJob;
use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\SecondListGoogleEvents;

class CalPropController extends Controller {
    
    public function index( Request $request ) {
        $controller = new CalendarController;
        return $controller->index( $request );
    }
    
    public function show( CalProp $calprop ) {

        $this->authorize( 'view', $calprop );
        
        // if_debug( $calprop->getAttributes() );

        BackButton::stackHere( request() );
        return view( 'groupware.calprop.show' )->with( 'calprop', $calprop );
    }
    
    public function create() {
        $this->authorize( 'create', CalProp::class );
    }
    
    public function store( CalPropRequest $request ) {
        $this->authorize( 'create', CalProp::class );
    }
    
    public function edit( CalProp $calprop ) {

        // if_debug( old() );
        $this->authorize( 'update', $calprop );
        
        $google_private_key_file = $calprop->google_private_key_file();

        BackButton::stackHere( request() );
        return view( 'groupware.calprop.input' )->with( 'calprop', $calprop )
                                                ->with( 'google_private_key_file', $google_private_key_file );
    }
    
    public function update( CalProp $calprop, CalPropRequest $request ) {

        $this->authorize( 'update', $calprop );

        $old_calprop = clone $calprop;
        $calprop = CalPropAction::updates( $calprop, $request );        

        // Google同期処理関係のイベント発行
        //
        if( $old_calprop->google_sync_on != $calprop->google_sync_on ) {
            
            
            session()->flash( 'error_message', 'Googleカレンダー関連の設定が変更されたので、一旦同期が解除になりました');
        }

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー表示設定・Google同期設定を変更しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);

        // return view( 'groupware.calprop.show' )->with( 'calprop', $calprop );
    }
    
    public function delete() {
        return die( __METHOD__ );
    }
    
    public function gsyncCheck( CalProp $calprop ) {
        
        $this->authorize( 'update', $calprop );

        // Google Calendar Events Listの確認
        //
        try {
            $client = new MyGoogleCalendarClient( $calprop );
            $optons = [ 'updatedMin' => Carbon::now()->subMinutes(5)->toAtomString() ];
            $lists = $client->list( $optons );
            
        } catch( Exception $e ) {
            $calprop->set_google_sync_check_NG();
            session()->flash( 'error_message', "Google同期できません。設定を確認してください。" );
            return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);
        }
        
        // Google Calendar Event Insert・Delete の確認
        //
        try {
            $today = now()->format( 'Y-m-d' );
            $schedule = new Schedule;
            $schedule->name = 'test';
            $schedule->start_date = $today;
            $schedule->end_date   = $today;
            $schedule->all_day    = 1;
            $schedule->memo       = "memo";
            $event  = $client->create( $schedule );
            $return = $client->delete( $calprop->google_calendar_id, $event->id );

        } catch( Exception $e ) {
            $calprop->set_google_sync_check_NG();
            session()->flash( 'error_message', "Googleへの書き込みができませんでした。Googleカレンダーの共有設定が「予定の変更」になっているか確認してください。" );
            return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);
        }
        $calprop->set_google_sync_check_OK();
        
        session()->flash( 'flash_message', "Google同期が可能です。今後、同期する場合は、Google同期ＯＮをしてください。" );
        return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);
        // return view( 'groupware.calprop.show' )->with( 'calprop', $calprop );
    }
    
    
    public function gsyncOn( CalProp $calprop ) {
        
        $this->authorize( 'update', $calprop );

        if( ! $calprop->checkGoogleSync() )  { return Response::deny(); }
    
        $calprop->google_sync_on = 1;
        $calprop->save();
        
        session()->flash( 'flash_message', "Googleカレンダー同期を開始しました" );
        return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);
    }
    
    public function gsync( CalProp $calprop ) {
        
        $this->authorize( 'view', $calprop );
        
        SecondListGoogleEvents::dispatch( $calprop );

        return redirect()->route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ]);
        // return view( 'groupware.calprop.show' )->with( 'calprop', $calprop );
        
    }
    
    public function gsyncAll() {
        
        if( ! is_debug() ) { 
            die( 'This Route is only available to developers');
        } else {
            if_debug( __METHOD__ );
        }
        
        // FirstScheduledJob::dispatch();
        FirstScheduledJob::dispatch()->delay( now()->addMinute() );

        // return redirect()->route( 'groupware.calendar.index' );
        return $this->index( request() );
    }
    
    
    
}
