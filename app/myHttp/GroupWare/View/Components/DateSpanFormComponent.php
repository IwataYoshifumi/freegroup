<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;

class DateSpanFormComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $start_name;  // 開始日のフォームの名前
    public $end_name;    // 終了日のフォームの名前
    
    public $start_value;    // 開始日のフォームの値
    public $end_value;      // 終了日のフォームの値


    public function __construct( $start = null, $end = null ) {
        
        $request = request();
        
        $this->start_name = ( ! empty( $start )) ? $start : 'start_date';
        $this->end_name   = ( ! empty( $end   )) ? $end   : 'end_date';
        
        $start_name = $this->start_name;
        $end_name   = $this->end_name;

        // if_debug( $request->all(), $start_name, isset( $request->$start_name ), $request->$start_name );

        $this->start_value = ( isset( $request->$start_name )) ? $request->$start_name : old( $start_name );
        $this->end_value   = ( isset( $request->$end_name   )) ? $request->$end_name   : old( $end_name   );

        // if_debug( $this );
    }
    
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        return view('components.DateSpanForm');
    }
}
