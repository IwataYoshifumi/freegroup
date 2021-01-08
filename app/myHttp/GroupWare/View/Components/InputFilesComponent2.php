<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;

class InputFilesComponent2 extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    //
    // コントローラ側で　App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass　をインスタンスして
    //　そのインスタンスをこのコンポーネントに渡してください。
    //

    // Fileクラスのid, file_name の配列
    //    
    public $form_name;          // フォームの名前
    public $files = [];         // MyFileクラスのコレクション
    public $attach_files = [];  // 添付するMyFile_idの配列

    //  コンストラクタの引数はキャメルケース必須
    //  アンダーバー付き、スネークケースだとと nresolvable dependency resolving [Parameter #0 :] というエラーになるエラーになる
    //
    public function __construct( ComponentInputFilesClass $input ) {

        $this->form_name    = $input->form_name;
        $this->files        = $input->files;
        $this->attach_files = $input->attach_files;
        
        // if_debug( __METHOD__, $input,  $this );        
    }
    
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        return view('components.groupware.models.file.input_files2');
    }
}
