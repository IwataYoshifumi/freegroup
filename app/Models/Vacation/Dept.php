<?php

namespace App\Models\Vacation;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vacation\User;

class Dept extends Model
{
    //
    
    //
    protected $fillable = [
        'name',
    ];
    
    public function users() {
        return $this->hasMany( 'App\Models\User' );
        
    }
    
    public function hasUser( User $user ) {
        // dd( $user->dept_id, $this->id );
        return $user->dept_id === $this->id;
    }
    
    public static function getArrayforSelect( $find = NULL, $no_null_value = NULL ) {
        
        if( ! is_null( $no_null_value )) {
            $array = [];
        } else {
            $array[""] = "";
        }
        if( is_null( $find )) {
            $depts = Dept::all();
        } else {
            $depts = new Dept();
            if( isset( $find['id']) ) {
                $depts = $depts->where( 'id', $find['id'] );
            }
            
            $depts = $depts->get();
        }
        
        foreach( $depts as $dept ) {
            $array[ $dept->id ] = $dept->name;
        }
        
        return $array;
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    
}
