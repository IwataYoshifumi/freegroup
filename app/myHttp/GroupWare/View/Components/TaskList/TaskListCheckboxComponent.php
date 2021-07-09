<?php

namespace App\myHttp\GroupWare\View\Components\TaskList;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

class TaskListCheckboxComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $form_name;      // フォームの名前
    public $form_id;        // フォームのID
    public $form_class;     // フォームのクラス
    
    public $values;         // TaskList IDの配列 
    
    /**
     *
     * 引数はUserのIDの配列 or Userのコレクション.
     *
     */
    public function __construct( $name = 'component_tasklists', $class = null, $values = [] ) {
        
        $this->form_name  = $name;
        $this->form_id    = $name . "_id";
        $this->form_class = $class;
        
        /* old があればそちらが優先 */

        $this->values = $values;
        
        //　タスクリスト検索フォームの初期値
        //
        $request = request();
        
        $type_form_name = $name . "_type";
        
        
        //  if_debug( $this, $values );
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.tasklist.checkboxes_task_list');
    }
}
