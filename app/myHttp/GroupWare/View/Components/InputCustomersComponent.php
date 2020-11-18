<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\Customer;

class InputCustomersComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $customers;  // Customerクラスのコレクション
    
    //
    public function __construct( $customers ) {
        
        if( is_array( $customers )) { 
            $this->customers = Customer::find( $customers );
        } else {
            $this->customers = $customers;
        }
        // dump( $customers, $this->customers );
    }
    

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.customer.input_customers');
    }
}
