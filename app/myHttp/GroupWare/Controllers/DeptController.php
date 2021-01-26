<?php

// namespace App\Http\Controllers;
namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;

use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Requests\DeptRequest;


class DeptController extends Controller {

    public function index( Request $request )  {

        $find = optional( $request )->find;
        $depts = Dept::search( $find );

        BackButton::setHere( $request );
        return view( 'groupware.dept.index' )->with( 'depts', $depts )
                                             ->with( 'find',  $find  );
    }

    public function create() {

        $this->authorize( 'create', Dept::class );
        
        BackButton::stackHere( request() );
        return view( 'groupware.dept.create' );
    }

    public function store(DeptRequest $request) {
        //
        
        $this->authorize( 'create', Dept::class );

        $dept = DB::transaction( function() use( $request ) {
            $dept = new Dept();
            $dept->name     = $request->name;
            $dept->save();
            return $dept;
        });
        
        Session::flash( 'flash_message', $request['name']."を追加しました。" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return  redirect()->route( 'dept.show' , ['dept' => $dept->id]  );
    }

    public function show(Dept $dept) {
        //
        BackButton::stackHere( request() );
        return view( 'groupware.dept.show' )->with( 'dept', $dept );
    }

    public function edit(Dept $dept) {
        //
        $this->authorize( 'update', $dept );
        
        BackButton::stackHere( request() );
        return view( 'groupware.dept.edit' )->with( 'dept', $dept );
    }

    public function update(DeptRequest $request, Dept $dept) {
        //
        $this->authorize( 'update', $dept );

        $old_name = $dept->name;
        DB::transaction( function() use( $dept, $request ) {
            
            $dept->name     = $request->name;
            $dept->save();

        });
        
        Session::flash( 'flash_message', $old_name."を".$request['name']."に変更しました" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return  redirect()->route( 'dept.show', [ 'dept' => $dept ] );
        
    }

    public function destroy(Dept $dept) {
        
        $this->authorize( 'delete', $dept );
        
        //
        $num = $dept->users->count();
        if( $num == 0 ) {
            BackButton::stackHere( request() );
            return view( 'groupware.dept.destroy' )->with( 'dept', $dept );
        } else {
            Session::flash( 'flash_message', $dept->name."には". $num. "名の従業員が登録されています。部署を削除できません" );
            return redirect()->route( 'dept.index' );
        }
    }
    
    public function destroyed(Dept $dept) {
        //
        $this->authorize( 'delete', $dept );
        
        $name = $dept->name;
        DB::transaction( function() use( $dept ) {
            Dept::find( $dept->id )->delete();;
            ACL::whereDept( $dept->id )->delete();
        });
        
        Session::flash( 'flash_message', "部署「". $name."」を削除しました。" );           
        BackButton::removePreviousSession();
        return redirect()->route( 'dept.index' );

    }
    
    
    
}