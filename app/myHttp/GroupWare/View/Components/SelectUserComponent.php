<?php

namespace App\myHttp\GroupWare\View\Components;

use Exception;
use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

use App\Http\Helpers\MyHelper;

class SelectUserComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $form_name;
    public $user_id = null;
    public $dept_id = null;
    public $user_name = null;
    
    public $form_id;
    public $form_class;
    public $index;
    public $depts = [];
    public $users = [ "" => "" ];
    
    //
    public function __construct( $array ) {

        if( ! is_array( $array ) or empty( optional( $array )['form_name'])) {
            throw new Exception( 'SelectUserComponent: construct Error 1 ' );
        }
        if( is_null( optional( $array )['form_name'])) {
            throw new Exception( 'SelectUserComponent: construct Error 2' );
        }
        
        $this->form_name  = $array['form_name'];
        $this->user_id    = optional( $array )['user_id'];
        $this->form_id    = optional( $array )['form_id'];
        $this->form_class = optional( $array )['form_class'];
        $this->index      = optional( $array )['index'];
        
        if( is_array( optional( $array )['depts'])) {
            $this->depts = $array['depts'];
        } else {
            $this->depts = toArrayWithEmpty( Dept::all() );
        }
        
        if( ! empty( $this->user_id )) {
            $user = User::find( $this->user_id );
            if( optional( $user->dept )->id ) { 
                $this->dept_id = $user->dept->id;
            } else {
                $this->user_name = $user->name;
            }
        }
        if( $this->dept_id ) {
            // $this->users = User::geet_array_for_select( [ 'dept_id' => $this->dept_id, ]);
            $this->users = toArrayWithEmpty( User::where( 'dept_id', $this->dept_id )->get() );
            

        } elseif( $this->user_name ) {
            $this->users = toArrayWithEmpty( User::search( [ 'name' => $this->user_name ] )->get() );
            // $this->users = User::geet_array_for_select( [ 'name' => $this->user_name ]);
        }
        
        // dump( "$this->index, $this->user_id" );
        
    }
    

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.user.select_user');
    }
}
