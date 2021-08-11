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

use App\myHttp\GroupWare\Requests\FacilityRequest;

use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Controllers\Search\SearchFacility;
use App\myHttp\GroupWare\Models\Actions\FacilityAction;

use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;

class FacilityController extends Controller {
    
    public function index( Request $request ) {

        if_debug( $request->all());

        //　検索初期条件
        //
        $find = ( isset( $request->find )) ? $request->find : [ 'user_id' => user_id(), 'auth' => 'writer' ];

        //　検索条件を入力せずに private な設備は検索できない
        //
        if( ! op($find)['auth'] or ! $find['user_id'] ) {
            $find['type']['private'] = null;
        }
        if( op( $find )['disabled'] ) { $find['not_use'] = 1; }
        
        $facilities = SearchFacility::search( $find );


        
        // dd( $facilities );
        // BackButton::setHere( $request );
        if( $request->from_menu ) {
            BackButton::setHere( $request );
        } else {
            BackButton::stackHere( $request );
        }
        return view( 'groupware.facility.index' )->with( 'facilities', $facilities )
                                                 ->with( 'find',      $find );

    }
    
    public function show( Facility $facility ) {
        
        BackButton::stackHere( request() );
        return view( 'groupware.facility.show' )->with( 'facility', $facility );
    }
    
    public function create() {
        
        $this->authorize( 'create', Facility::class );

        $facility    = new Facility;
        $access_list = new AccessList;
        
        //　初期値設定
        //
        $facility->background_color = "#faebd7";
        $facility->text_color       = "#000000";
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        
        BackButton::stackHere( request() );
        return view( 'groupware.facility.input' )->with( 'facility', $facility )
                                                 ->with( 'access_list', $access_list )
                                                 ->with( 'component_input_files', $component_input_files );
    }
    
    public function store( FacilityRequest $request ) {

        $this->authorize( 'create', Facility::class );

        $facility = FacilityAction::creates( $request );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "設備「". $facility->name . "」を作成しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.facility.show', [ 'facility' => $facility->id ]);
    }
    
    public function edit( Facility $facility ) {
        
        $this->authorize( 'update', $facility );
            
        $access_list = $facility->access_list();
        $component_input_files = new ComponentInputFilesClass( 'attach_files', $facility->files  );
        
        BackButton::stackHere( request() );
        return view( 'groupware.facility.input' )->with( 'facility', $facility )
                                                 ->with( 'access_list', $access_list )
                                                 ->with( 'component_input_files', $component_input_files );
    }
    
    public function update( Facility $facility, FacilityRequest $request ) {

        $this->authorize( 'update', $facility );
        
        $old_facility = clone $facility;
        
        $facility = FacilityAction::updates( $facility, $request );

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "設備「". $facility->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.facility.show', [ 'facility' => $facility->id ]);
    }
    
    public function delete( Facility $facility ) {
        
        $this->authorize( 'delete', $facility );

        BackButton::stackHere( request() );
        return view( 'groupware.facility.delete' )->with( 'facility', $facility );

    }
    
    public function deleted( Facility $facility, FacilityRequest $request ) {
        
        $this->authorize( 'delete', $facility );

        FacilityAction::deletes( $facility );

        BackButton::removePreviousSession();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "設備「". $facility->name . "」と関連スケジュール等は完全に削除されました" );

        return self::index( $request );
        
        return redirect()->route( 'groupware.facility.list' );
    }
    

    
    
    
}
