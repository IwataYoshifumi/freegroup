<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Tasks;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Spatie\GoogleCalendar\Event;


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

use  App\Http\Requests\CalendarRequest;

class GoogleCalendarController extends Controller
{
    // public $calendarId = 'c_6f77l677cbv1j7n3bogprlr9eg@group.calendar.google.com';
    public $calendarId = 'ndc3mlttvdhjt7qlfa0r54355c@group.calendar.google.com';  // たんたんFX　APIテスト
    // public $calendarId = 'c_egacbi4sfll9p7ve9ee1t54c2s@group.calendar.google.com';
    
    public function create() {
        return view( 'test.google.calendar.input' );
    }
    
    public function store( CalendarRequest $request ) {
        
        $client = $this->getClient();
        $service = new Google_Service_Calendar( $client );
        
        $inputs = [ 'start' => [ 'date' => $request->inputs['start'], 'timeZone' => 'Asia/Tokyo' ], 
                    'end'   => [ 'date' => $request->inputs['end'],   'timeZone' => 'Asia/Tokyo' ],
                    'summary' => $request->inputs['summary'],
                    'location' => $request->inputs['location'],
                    'description' => $request->inputs['description'],
                ];
        
        
        $event = new Google_Service_Calendar_Event( $inputs );
        // dd( $inputs, $request->inputs, $event, $this->calendarId );
        dump( $request->inputs, $event, $this->calendarId );
        
        $event = $service->events->insert($this->calendarId, $event );
        
        return view( 'test.google.calendar.input' );
        // return redirect()->route( 'calendar.index' );
    }
    
    public function edit( $gid ) {
        
        dump( $gid );
        $client = $this->getClient();
        $service = new Google_Service_Calendar( $client );
        $event = $service->events->get( $this->calendarId, $gid );
        dump( $event );
        
        if( ! empty( $event )) {
            $inputs['summary']  = $event->summary;
            $inputs['location'] = $event->getLocation();
            $inputs['description'] = $event->getDescription();
            
            $start = $event->getStart();
            $end   = $event->getEnd();
            
            if( is_null( $start->getDate() )) {
                $inputs['start'] = Carbon::create( $start->getDateTime() )->format( 'Y-m-d' );
                $inputs['end']   = Carbon::create( $end->getDateTime() )->format( 'Y-m-d' );
                $inputs['start_time'] = Carbon::create( $start->getDateTime() )->format( 'H:i' );
                $inputs['end_time']   = Carbon::create( $end->getDateTime()   )->format( 'H:i' );               
                
            } else {
                $inputs['start']    = $event->getStart()->getDate();
                $inputs['end']      = $event->getEnd()->getDate();
                $inputs['start_time'] = null;
                $inputs['end_time']   = null;               

            }        
        }
        $inputs['gid'] = $gid;
        
        return view( 'test.google.calendar.input' )->with( 'inputs', $inputs );
        
    }
    
    public function update( $gid, CalendarRequest $request ) {

        $client = $this->getClient();
        $service = new Google_Service_Calendar( $client );
        
        $inputs = $request->inputs;
        
        $start = new Google_Service_Calendar_EventDateTime();
        $start->setTimeZone( 'Asia/Tokyo' );
        $end   = clone $start;
        
        if( is_null( $inputs['start_time'] )) {
            $start->setDate( $inputs['start'] );
            $end->setDate( $inputs['end'] );
        } else {
            $start->setDateTime( Carbon::create( $inputs['start']."T".$inputs['start_time'] )->format( 'c' ));
            $end->setDateTime( Carbon::create( $inputs['end']."T".$inputs['end_time'] )->format( 'c' ));
        }
        // dd( $start, $end );
        $event = $service->events->get( $this->calendarId, $gid );
        $event->setSummary( $inputs['summary'] );
        $event->setStart( $start );
        $event->setEnd( $end );
        $event->location = $request->inputs['location'];
        $event->description = $request->inputs['description'];
        // dd( $event );

        $updatedEvent = $service->events->update( $this->calendarId, $gid, $event );
        
        // flash_message( '更新しました' );
        // return view( 'test.google.calendar.index' );
        return redirect()->route( 'calendar.index' );
        
    }
    
    
    // 
    //
    public function index2(){
        
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        // dd( $calendarId );
        $optParams = [];
        $results = $service->events->listEvents($this->calendarId, $optParams);
        $events = $results->getItems();
        dump( $results, $events );
        foreach($events as $event){
            echo $event->getSummary().'';
        }
        return view( 'test.google.calendar.index' );

    }
    
    
    public function index3(){
        
        
        $client = $this->getClient();        
        $service = new Google_Service_Calendar($client);
        // dd( $client, $service );
        
        $encoded_calendarId = urlencode( $this->calendarId );
        $url = "https://www.googleapis.com/calendar/v3/calendars/" . $encoded_calendarId . "/events?";
        
        $query = [  
                    'timeMin' => date( 'c', strtotime( '2019-01-01'   )),
                    'timeMax' => date( 'c', strtotime( '2020-12-31' )),
                    'maxResults' => 100,
                    // 'orderBy'    => 'startTime',
                    // 'singleEvents' => true,
                    ];

        // $data = file_get_contents( $url. http_build_query( [] ), true );
        // $dump( $data );

        $results = $service->events->listEvents( $this->calendarId, $query );
        // $results = $service->events->listEvents( $this->calendarId, [] );
        $events = $results->getItems();
        dump( $results, $events );
        
        //　タスクを読込む
        //
        $service = new Google_Service_Tasks( $client );
        $tasks = $service->tasklists->listTasklists( [ 'maxResults' => 100 ] );
        dump( $tasks );

        return view( 'test.google.calendar.index' )->with( 'events', $events );

    }
    
    public function index() {

        return self::index3();
        // return self::index2();
    }

    public function getClient() {

        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API plus Laravel');
    
        if( Route::currentRouteName() == 'calendar.index' ) {
            $client->setScopes([ Google_Service_Calendar::CALENDAR_READONLY, Google_Service_Tasks::TASKS_READONLY ] );
        } else {
            $client->setScopes(Google_Service_Calendar::CALENDAR);
            // $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
            
        }
        // $client->setAuthConfig(storage_path('app/google-calendar/google-cloud-key.json'));
        $client->setAuthConfig(storage_path('app/google-calendar/tantan-fx-project-0022f330b202.json'));
        return $client;

    }
    
    public function getClientForWrite() {
        
        $json_path = __DIR__.'/app/google-calendar/google-cloud-key.json'; // jsonファイルのパスをここに書いてください。
        $json_string = file_get_contents($json_path, true);
     
        $json = json_decode($json_string, true);
     
        $private_key = $json['private_key'];
        $client_email = $json['client_email'];
     
        $scopes = array(Google_Service_Calendar::CALENDAR);
     
        $credentials = new Google_Auth_AssertionCredentials(
            $client_email,
            $scopes,
            $private_key
        );
      
        
        
        $client = new Google_Client();
 

        $client->setApplicationName('Google Calendar PHP API');
        $client->setAssertionCredentials($credentials);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion();
        }
        return $client;
        
    }
    
    
    
}
