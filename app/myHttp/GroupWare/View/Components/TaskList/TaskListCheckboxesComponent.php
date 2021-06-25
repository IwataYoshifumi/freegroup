<?php

namespace App\myHttp\GroupWare\View\Components\TaskList;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

class TaskListCheckboxesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $tasklists;         // TaskListのIDの配列
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;

    public $list_of_tasklists; // 検索対象のカレンダーのTaskListインスタンスのコレクション

    // public $search_tasklists_conditions; // カレンダーを検索する条件（　非表示カレンダーを対象・非対称、カレンダーのアクセス権限（owner, writer, reader )
    
    /**
     *
     * 引数はTaskListのIDの配列 or TaskListのコレクション.
     *
     */
    public function __construct( $tasklists , $name = 'tasklists', $button = 'カレンダー検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        
        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $tasklists = old( $name ); }
        
        //　デフォルトのカレンダー
        //
        if( empty( $tasklists )) { 
            $this->tasklists = [];
        } elseif( is_array( $tasklists )) { 
            // $this->tasklists = TaskList::find( $tasklists ); 
            $this->tasklists = $tasklists; 
        } else { 
            $this->tasklists = $tasklists; 
        }


        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.tasklist.tasklist_checkboxes');
    }
}
