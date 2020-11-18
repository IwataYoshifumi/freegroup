<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

class FindUserComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $form_name;
    public $form_class;
    public $form_id;
    public $form_default;
    public $form_array;
    
    public $dept_form_id;
    
    //
    public function __construct( $options ) {
        
        
        $this->form_name    = ( isset( $options['name']    )) ? $options['name']    : "component_user_id";
        $this->form_class   = ( isset( $options['class']   )) ? $options['class']   : "form-control ".$this->name;
        $this->form_id      = ( isset( $options['id']      )) ? $options['id'   ]   : $this->name;
        $this->form_default = ( isset( $options['default'] )) ? $options['default'] : "";
        $this->form_array   = ( isset( $options['array'] ))   ? $options['array']   : [];

        $this->dept_form_id      = ( isset( $options['dept_form_id']      )) ? $options['dept_form_id'   ]   : NULL;
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.user.find_user');
    }
}
