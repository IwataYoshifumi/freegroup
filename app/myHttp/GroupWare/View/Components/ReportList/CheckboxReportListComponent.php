<?php

namespace App\myHttp\GroupWare\View\Components\ReportList;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListReportListRole;

class CheckboxReportListComponent extends Component {
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $report_lists;         // 選択されている ReportListクラスのインスタンスのコレクション
    public $name;     // フォームの名前
    public $button;  // ボタンラベル
    public $form_class;
    
    /**
     *
     * 引数はReportListのIDの配列 or ReportListのコレクション.
     *
     */
    public function __construct( $reportlists ,$name = 'report_lists', $button = '日報リスト', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        $report_lists = $reportlists;

        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $report_lists = old( $name ); }
        
        if( empty( $report_lists ))        { $this->report_lists = [];                   } 
        elseif( is_array( $report_lists )) { $this->report_lists = ReportList::find( $report_lists ); } 
        else                               { $this->report_lists = $report_lists;               }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.report_list.checkboxes_report_list');
    }
}