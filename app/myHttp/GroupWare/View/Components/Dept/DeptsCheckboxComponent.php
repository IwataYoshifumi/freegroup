<?php

namespace App\myHttp\GroupWare\View\Components\Dept;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

class DeptsCheckboxComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $depts;         // 選択されている Deptクラスのインスタンスのコレクション
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;
    
    /**
     *
     * 引数はDeptのIDの配列 or Deptのコレクション.
     *
     */
    public function __construct( $depts , $name = 'depts', $button = '部署検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        if( empty( $depts ))        { $this->depts = [];                   } 
        elseif( is_array( $depts )) { $this->depts = Dept::find( $depts ); } 
        else                        { $this->depts = $depts;               }

        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $this->depts = old( $name ); }
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.dept.depts_checkboxes');
    }
}
