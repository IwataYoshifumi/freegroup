<?php

namespace App\myHttp\GroupWare\View\Components\Customer;

use Illuminate\View\Component;

use App\myHttp\GroupWare\Models\Customer;

class CustomersCheckboxComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $customers;  // 選択されている Customerクラスのインスタンスのコレクション
    public $name;       // フォームの名前
    public $button;     // ボタンラベル
    public $form_class;
    
    /**
     *
     * 引数はCustomerのIDの配列 or Customerのコレクション.
     *
     */
    public function __construct( $customers , $name = 'customers', $button = '顧客検索', $formclass = 'col-12' ) {
        
        $this->name       = $name;
        $this->button     = $button;
        $this->form_class = $formclass;
        
        if( empty( $customers ))        { $this->customers = [];                   } 
        elseif( is_array( $customers )) { $this->customers = Customer::find( $customers ); } 
        else                        { $this->customers = $customers;               }

        /* old があればそちらが優先 */
        if( ! empty( old( $name ))) { $this->customers = old( $name ); }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render() {
        
        return view('components.groupware.models.customer.customers_checkboxes');
    }
}
