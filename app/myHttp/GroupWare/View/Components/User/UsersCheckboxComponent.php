<?php

namespace App\myHttp\GroupWare\View\Components\User;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

class UsersCheckboxComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $users;         // 選択されている Userクラスのインスタンスのコレクション
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;
    
    /**
     *
     * 引数はUserのIDの配列 or Userのコレクション.
     *
     */
    public function __construct( $users , $name = 'users', $button = '社員検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        if( empty( $users ))        { $this->users = [];                   } 
        elseif( is_array( $users )) { $this->users = User::find( $users ); } 
        else                        { $this->users = $users;               }

        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $this->users = old( $name ); }
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.user.users_checkboxes');
    }
}
