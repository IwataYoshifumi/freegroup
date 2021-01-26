<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\Customer;

class InputUserssComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $users;  // Customerクラスのコレクション
    public $name;   // フォームの名前
    
    //
    public function __construct( $users, $name = NULL ) {
        
        if( is_array( $users )) { 
            $this->users = Customer::find( $users );
        } else {
            $this->users = $users;
        }
        if( is_null( $name )) {
            $this->name = "users";
        } else {
            $this->name = $name;
        }
    }
    

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.user.input_userss');
    }
}
