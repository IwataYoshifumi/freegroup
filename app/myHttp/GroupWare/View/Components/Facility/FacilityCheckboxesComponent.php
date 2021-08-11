<?php

namespace App\myHttp\GroupWare\View\Components\Facility;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Facility;

class FacilityCheckboxesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $facilities;         // FacilityのIDの配列
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;

    public $list_of_facilities; // 検索対象の設備のFacilityインスタンスのコレクション

    // public $search_facilities_conditions; // 設備を検索する条件（　非表示設備を対象・非対称、設備のアクセス権限（owner, writer, reader )
    
    /**
     *
     * 引数はFacilityのIDの配列 or Facilityのコレクション.
     *
     */
    public function __construct( $facilities , $name = 'facilities', $button = '設備検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $facilities = old( $name ); }

        //　デフォルトの設備
        //
        if( empty( $facilities )) { 
            $this->facilities = [];
        } elseif( is_array( $facilities )) { 
            // $this->facilities = Facility::find( $facilities ); 
            $this->facilities = $facilities; 
        } else { 
            $this->facilities = $facilities; 
        }

        // if_debug( $this );

        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.facility.facility_checkboxes');
    }
}
