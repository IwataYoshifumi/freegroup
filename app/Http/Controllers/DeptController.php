<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

// use App\Models\Vacation\Dept;
use App\Models\Dept;
// use App\Http\Requests\Vacation\DeptRequest;
use App\Http\Requests\DeptRequest;
use App\Http\Helpers\BackButton;

class DeptController extends Controller {

    public function index( Request $request )  {

        BackButton::setHere( $request );
        $find = optional( $request )->find;
        // dump( optional( $request )->find );
        $depts = Dept::search( $find );
        
        // return view( 'vacation.dept.index' )->with( 'depts', $depts );
        return view( 'dept.index' )->with( 'depts', $depts )
                                   ->with( 'find',  $find  );
    }

    public function create() {
        BackButton::stackHere( request() );
        // return view( 'vacation.dept.create' );
        return view( 'dept.create' );
    }

    public function store(DeptRequest $request) {
        //
        DB::transaction( function() use( $request ) {
            
            $dept = new Dept();
        
            $dept->name     = $request->name;
            $dept->save();

        });
        
        Session::flash( 'flash_message', $request['name']."を追加しました。" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        // return  redirect()->route( 'vacation.dept.index' );
        return  redirect()->route( 'dept.index' );
    }

    public function show(Dept $dept) {
        //
        BackButton::stackHere( request() );
        // return view( 'vacation.dept.show' )->with( 'dept', $dept );
        return view( 'dept.show' )->with( 'dept', $dept );
    }

    public function edit(Dept $dept) {
        //
        BackButton::stackHere( request() );
        // return view( 'vacation.dept.edit' )->with( 'dept', $dept );
        return view( 'dept.edit' )->with( 'dept', $dept );
    }

    public function update(DeptRequest $request, Dept $dept) {
        //
        $old_name = $dept->name;
        DB::transaction( function() use( $dept, $request ) {
            
            $dept->name     = $request->name;
            $dept->save();

        });
        
        Session::flash( 'flash_message', $old_name."を".$request['name']."に変更しました" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        // return  redirect()->route( 'vacation.dept.show', [ 'dept' => $dept ] );
        return  redirect()->route( 'dept.show', [ 'dept' => $dept ] );
        
    }

    public function destroy(Dept $dept) {
        //
        $num = $dept->users->count();
        if( $num == 0 ) {
            BackButton::stackHere( request() );
            // return view( 'vacation.dept.destroy' )->with( 'dept', $dept );
            return view( 'dept.destroy' )->with( 'dept', $dept );
        } else {
            Session::flash( 'flash_message', $dept->name."には". $num. "名の従業員が登録されています。部署を削除できません" );
            // return redirect()->route( 'vacation.dept.index' );
            return redirect()->route( 'dept.index' );
        }
            
    }
    
    public function destroyed(Dept $dept) {
        //
        $num = $dept->users->count();
        if( $num == 0 ) {
            $name = $dept->name;
            DB::transaction( function() use( $dept ) {
                Dept::destroy( $dept->id );
            });
            
            Session::flash( 'flash_message', "部署「". $name."」を削除しました。" );           
            BackButton::removePreviousSession();
            // return redirect()->route( 'vacation.dept.index' );
            return redirect()->route( 'dept.index' );
        } else {
            Session::flash( 'error_message', $dept->name."には". $num. "名の従業員が登録されています。部署を削除できません" );
            // return redirect()->route( 'vacation.dept.index' );
            return redirect()->route( 'dept.index' );
        }
            
    }
    
}