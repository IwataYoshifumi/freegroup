<?php

namespace App\myHttp\GroupWare\View\Components\Calendar;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

class CalendarCheckboxesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $calendars;         // CalendarのIDの配列
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;

    public $list_of_calendars; // 検索対象のカレンダーのCalendarインスタンスのコレクション

    // public $search_calendars_conditions; // カレンダーを検索する条件（　非表示カレンダーを対象・非対称、カレンダーのアクセス権限（owner, writer, reader )
    
    /**
     *
     * 引数はCalendarのIDの配列 or Calendarのコレクション.
     *
     */
    public function __construct( $calendars , $name = 'calendars', $button = 'カレンダー検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $calendars = old( $name ); }

        //　デフォルトのカレンダー
        //
        if( empty( $calendars )) { 
            $this->calendars = [];
        } elseif( is_array( $calendars )) { 
            // $this->calendars = Calendar::find( $calendars ); 
            $this->calendars = $calendars; 
        } else { 
            $this->calendars = $calendars; 
        }


        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.calendar.calendar_checkboxes');
    }
}
