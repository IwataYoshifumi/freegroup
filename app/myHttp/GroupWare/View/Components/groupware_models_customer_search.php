<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

class groupware_models_customer_search extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.customer.search');
    }
}
