<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\Customer;

class InputSchedulesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $schedules;  // Customerクラスのコレクション
    
    //
    public function __construct( $schedules ) {
        
        // if( is_array( $schedules )) { 
        //     $this->schedules = Customer::find( $schedules );
        // } else {
        //     $this->schedules = $schedules;
        // }
        $this->schedules = $schedules;
        // dump( $schedules, $this->schedules );
    }
    

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.schedule.input_schedules');
    }
}
