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

use App\myHttp\GroupWare\Requests\ScheduleTypeRequest;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ScheduleType;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;


class ScheduleTypeController extends Controller {
    
    // 　ルーティングコントローラー
    //
    public function index() {
        
        // スケジュール種別の初期化
        if( ScheduleType::init_schedule_types( auth( 'user' )->user() ) ) {
            return redirect()->route( 'groupware.schedule.type.index' );
        }
        
        $user = auth( 'user' )->user();
        $schedule_types = $user->schedule_types;
        // dump( $user, $user->schedule_types );
        
        BackButton::stackHere( request() );
        // BackButton::removePreviousSession();
        return view( 'groupware.schedule_type.index' )->with( 'schedule_types', $schedule_types );
    }
    
    public function create() {
        // BackButton::stackHere( request() );
        return view( 'groupware.schedule_type.create' );
    }
    
    public function store( ScheduleTypeRequest $request ) {
        $input = $request->input;
        // dump( $request );

        $schedule_type = new ScheduleType( $input );
        $schedule_type->user_id = auth('user')->id();
        $schedule_type->class   = 'original';
        // $schedule_type->google_private_key_file;
        $schedule_type->save();

        //　ファイル保存
        //
        if( ! empty( $request->google_private_key_file )) {
            $file = $request->file( 'google_private_key_file' );
            // dump( $file );
            $path = $file->store('');
            $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->id() ];
            $f = MyFile::create( $value );
            // dd( $value );
            
            // $schedule_type->google_private_key_file()->save( $f );
            // $schedule_type->google_private_key_file()->create( $value );
            // $schedule_type->file_id = $f->id;
            $schedule_type->files()->sync( [$f->id] );
        } 

        $schedule_type->save();


        #dump( $schedule_type, $request->all() );

        BackButton::removePreviousSession();
        session::flash( 'flash_message', 'スケジュール種別「'.$schedule_type->name.'」を追加しました。');
        return redirect()->route( 'groupware.schedule.type.index' );
        // return view( 'groupware.schedule_type.create' );
        
    }
    
    public function edit( ScheduleType $schedule_type, Request $request ) {
        // dd( $schedule_type, $schedule_type->files );
        // dd( request() );
        BackButton::stackHere( $request );
        return view( 'groupware.schedule_type.edit' )->with( 'schedule_type', $schedule_type );   
    }
    
    public function update( ScheduleType $schedule_type, ScheduleTypeRequest $request ) {
        
        DB::transaction( function() use ( $request, $schedule_type ) {
        
            $schedule_type->update( $request->input );
            $schedule_type->save();

            //　ファイル保存
            //
            if( ! empty( $request->google_private_key_file )) {
                $file = $request->file( 'google_private_key_file' );
                dump( $file );
                $path = $file->store('');
                $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->id() ];
                $f = MyFile::create( $value );
                $schedule_type->files()->sync( [$f->id] );
            } 
        });
        #dump( $schedule_type, $request->all() );

        BackButton::removePreviousSession();
        session::flash( 'flash_message', 'スケジュール種別「'.$schedule_type->name.'」を修正しました');
        return redirect()->route( 'groupware.schedule.type.index' );
        // return view( 'groupware.schedule_type.create' );
    }
    
    public function delete() {
        
    }
    
    
}
