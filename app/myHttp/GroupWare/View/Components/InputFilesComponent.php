<?php

namespace App\myHttp\GroupWare\View\Components;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\File as MyFile;

class InputFilesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    // Fileクラスのid, file_name の配列
    //    
    public $attached_files;
    
    //  コンストラクタの引数はキャメルケース必須
    //  アンダーバー付き、スネークケースだとと nresolvable dependency resolving [Parameter #0 :] というエラーになるエラーになる
    //
    public function __construct( $attachedFiles ) {

        // if_debug( $attachedFiles );
        if( count( $attachedFiles )) {
            #$ids = null;
            #if_debug( $attachedFiles );
            foreach( $attachedFiles as $i => $file ) {
                $ids[$i] = ( is_array( $file )) ? $file['id'] : $file;
            }
            #if_debug( isset( $ids ) ? $ids : null );
            $this->attached_files = MyFile::select( ['id', 'file_name'] )->find( $ids )->toArray();
            // if_debug( $attached_files );
        } else {
            $this->attached_files = [];
        }
        
    }
    
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        return view('components.groupware.models.file.input_files');
    }
}
