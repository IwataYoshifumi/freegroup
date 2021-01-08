<?php
namespace App\myHttp\GroupWare\Models\SubClass;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

class ComponentInputFilesClass  {

    public $form_name;        // フォームの名前
    public $files;            // 添付ファイル＆アップロードファイルの　Fileインスタンスのコレクション
    public $attach_files;     // 添付するFile_id　の配列
    public $componet_inputs;  // Requestからの入力（コンポーネントフォームの入力値）
    
    //　files : FileモデルのIDの配列
    //
    public function __construct( $form_name = 'files', $attached_files = [] ) {

        $request = request();
        // if_debug( $request, $attached_files);

        $this->form_name  = $form_name;
        $componet_inputs = ( op( $request )->component_input_files ) ? $request->component_input_files : null;
        $attach_file_ids = ( op( $request )->$form_name            ) ? $request->$form_name            : [];
        $file_ids        = ( op( $componet_inputs )['files']       ) ? $componet_inputs['files']       : [];

        // Attached_Files がファイルオブジェクトなら一旦 File_id の配列に変換
        // フォームでアップロードされたファイルも含めて
        //
        $attached_file_ids = ( is_object( $attached_files )) ? $attached_files->pluck('id')->toArray() : $attached_files;
        // if_debug( $attached_file_ids, $attach_file_ids );
        
        $attach_file_ids = Arr::collapse([ $attach_file_ids, $attached_file_ids] );

        // if_debug( $attach_file_ids, old() );

        // File を検索
        //
        $file_ids = Arr::collapse( [$attach_file_ids, $file_ids] );
        $files    = MyFile::whereIn( 'id', $file_ids )->get();

        $this->componet_inputs = $componet_inputs;
        $this->attach_files    = $attach_file_ids;
        $this->files           = $files->load( 'user', 'user.dept' );

        // if_debug( $this );
    }
    
    public static function getDetachFiles() {
        
        
        
    }
    
}

