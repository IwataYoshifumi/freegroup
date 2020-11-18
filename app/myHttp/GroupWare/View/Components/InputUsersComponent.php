<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;

class InputUsersComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $users;  // Customerクラスのコレクション
    
    //
    public function __construct( $users ) {
        
        if( is_array( $users )) { 
            $this->users = User::find( $users );
        } else {
            $this->users = $users;
        }
        // dump( $users, $this->users, User::find( $users ) );
    }
    

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.user.input_users');
    }
}
