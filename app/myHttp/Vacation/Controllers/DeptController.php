<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

use App\Models\Vacation\Dept;
use App\Http\Requests\Vacation\DeptRequest;
use App\Http\Helpers\BackButton;

class DeptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {

        BackButton::setHere( $request );
        
        $depts = Dept::all();
        return view( 'vacation.dept.index' )->with( 'depts', $depts );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'vacation.dept.create' );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeptRequest $request)
    {
        //
        DB::transaction( function() use( $request ) {
            
            $dept = new Dept();
        
            $dept->name     = $request->name;
            $dept->save();

        });
        
        Session::flash( 'flash_message', $request['name']."を追加しました。" );
        session()->regenerateToken();
        return  redirect()->route( 'vacation.dept.index' );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dept  $dept
     * @return \Illuminate\Http\Response
     */
    public function show(Dept $dept)
    {
        //
        return view( 'vacation.dept.show' )->with( 'dept', $dept );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dept  $dept
     * @return \Illuminate\Http\Response
     */
    public function edit(Dept $dept)
    {
        //

        return view( 'vacation.dept.edit' )->with( 'dept', $dept );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dept  $dept
     * @return \Illuminate\Http\Response
     */
    public function update(DeptRequest $request, Dept $dept)
    {
        //
        $old_name = $dept->name;
        DB::transaction( function() use( $dept, $request ) {
            
            $dept->name     = $request->name;
            $dept->save();

        });
        
        Session::flash( 'flash_message', $old_name."を".$request['name']."に変更しました" );
        session()->regenerateToken();
        return  redirect()->route( 'vacation.dept.show', [ 'dept' => $dept ] );
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dept  $dept
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dept $dept)
    {
        //
        $num = $dept->users->count();
        if( $num == 0 ) {
            return view( 'vacation.dept.destroy' )->with( 'dept', $dept );
        } else {
            Session::flash( 'flash_message', $dept->name."には". $num. "名の従業員が登録されています。部署を削除できません" );
            return redirect()->route( 'vacation.dept.index' );
        }
            
    }
    
    public function destroyed(Dept $dept)
    {
        //
        $num = $dept->users->count();
        if( $num == 0 ) {
            $name = $dept->name;
            DB::transaction( function() use( $dept ) {
                Dept::destroy( $dept->id );
            });
            
            Session::flash( 'flash_message', "部署「". $name."」を削除しました。" );           
            return redirect()->route( 'vacation.dept.index' );
        } else {
            Session::flash( 'error_message', $dept->name."には". $num. "名の従業員が登録されています。部署を削除できません" );
            return redirect()->route( 'vacation.dept.index' );
        }
            
    }
}
