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


        /* old があればそちらが優先 */
        // dump( 'pre', $customers, old( 'customers') );
        if( ! empty( old( $name ))) { $customers = old( $name ); }
        // dump( 'post', $customers, old( 'customers') );
        
        if( empty( $customers ))        { 
            // dump( 'empty');
            $this->customers = [];                   
        } elseif( is_array( $customers )) { 
            // dump( 'is_array');

            $this->customers = Customer::find( $customers ); 
        } else { 
            // dump( 'else');
            $this->customers = $customers;               
        }
        // dump( $this->customers, old( 'customers') );
        


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
