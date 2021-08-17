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

use App\myHttp\GroupWare\Requests\CalendarRequest;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\CalendarAction;

use App\myHttp\GroupWare\Controllers\Search\SearchCalender;

class CalendarController extends Controller {
    
    public function index( Request $request ) {

        if_debug( $request->all() );
        //　検索初期条件
        //
        $find = ( isset( $request->find )) ? $request->find : [ 'user_id' => user_id(), 'keyword' => 'canWrite' ];

        //　検索条件を入力せずに private なカレンダーは検索できない
        //
        if( ! op($find)['keyword'] or ! $find['user_id'] ) {
            $find['type']['private'] = null;
        }
        if( op( $find )['disabled'] ) { $find['not_use'] = 1; }
        
        $calendars = SearchCalender::search( $find );
        $calendars->load( [ 'calprops' => function( $query ) { $query->where( 'user_id', user_id() ); } ] );
        
        BackButton::stackHere( $request );
        return view( 'groupware.calendar.index' )->with( 'calendars', $calendars )
                                                 ->with( 'find',      $find );
    }
    
    public function show( Calendar $calendar ) {
        
        $access_list = $calendar->access_list();
        // if_debug( $access_list->isOwner( user_id() ));
        // if_debug( $access_list->isWriter( user_id() ));
        // if_debug( $access_list->isReader( user_id() ));
        // if_debug( $access_list->CanWrite( user_id() ));
        // if_debug( $access_list->canRead( user_id() ));
        
        BackButton::stackHere( request() );
        return view( 'groupware.calendar.show' )->with( 'calendar', $calendar );
    }
    
    public function create() {
        
        $this->authorize( 'create', Calendar::class );

        $calendar    = new Calendar;
        $access_list = new AccessList;
        
        BackButton::stackHere( request() );
        return view( 'groupware.calendar.input' )->with( 'calendar', $calendar )
                                                 ->with( 'access_list', $access_list );
    }
    
    public function store( CalendarRequest $request ) {

        $this->authorize( 'create', Calendar::class );

        $calendar = CalendarAction::creates( $request );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー「". $calendar->name . "」を作成しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.calendar.show', [ 'calendar' => $calendar->id ]);
    }
    
    public function edit( Calendar $calendar ) {
        
        $this->authorize( 'update', $calendar );
            
        $access_list = $calendar->access_list();
        
        BackButton::stackHere( request() );
        return view( 'groupware.calendar.input' )->with( 'calendar', $calendar )
                                                 ->with( 'access_list', $access_list );
    }
    
    public function update( Calendar $calendar, CalendarRequest $request ) {

        $this->authorize( 'update', $calendar );
        
        $old_calendar = clone $calendar;
        
        $calendar = CalendarAction::updates( $calendar, $request );
        if(( $calendar->disabled != $old_calendar->disabled ) and $calendar->disabled ) {
            // Google 同期解除ジョブを発行

        }

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー「". $calendar->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.calendar.show', [ 'calendar' => $calendar->id ]);
    }
    
    public function delete( Calendar $calendar ) {
        
        $this->authorize( 'delete', $calendar );

        BackButton::stackHere( request() );
        return view( 'groupware.calendar.delete' )->with( 'calendar', $calendar );

    }
    
    public function deleted( Calendar $calendar, CalendarRequest $request ) {
        
        $this->authorize( 'delete', $calendar );

        CalendarAction::deletes( $calendar );

        BackButton::removePreviousSession();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "カレンダー「". $calendar->name . "」と関連スケジュール等は完全に削除されました" );

        return self::index( $request );
        
        return redirect()->route( 'groupware.calendar.list' );
    }
    
    
    
}
