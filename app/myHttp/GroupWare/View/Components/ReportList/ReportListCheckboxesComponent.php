<?php

namespace App\myHttp\GroupWare\View\Components\ReportList;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\CalProp;

class ReportListCheckboxesComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $report_lists;         // ReportListのIDの配列
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;

    public $list_of_report_lists; // 検索対象の日報リストのReportListインスタンスのコレクション

    // public $search_report_lists_conditions; // 日報リストを検索する条件（　非表示日報リストを対象・非対称、日報リストのアクセス権限（owner, writer, reader )
    
    /**
     *
     * 引数はReportListのIDの配列 or ReportListのコレクション.
     *
     */
    public function __construct( $reportLists , $name = 'report_lists', $button = '日報リスト検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $reportlists = old( $name ); }

        //　デフォルトの日報リスト
        //
        if( empty( $reportLists )) { 
            $this->report_lists = [];
        } elseif( is_array( $reportLists )) { 
            // $this->report_lists = ReportList::find( $reportLists ); 
            $this->report_lists = $reportLists; 
        } else { 
            $this->report_lists = $reportLists; 
        }
        
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        // return 'aaa';
        return view('components.groupware.models.report_list.report_list_checkboxes');
    }
}
