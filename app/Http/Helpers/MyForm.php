<?php 

namespace App\Http\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class MyForm {
    
    //  リスト表示フォームを生成
    //
    static public function index( $options ) {
        
        //　値の初期化
        //
        $rows         = $options['rows'];    //　DB形式
        $columns      = $options['columns'];
        $show = [];
        $form = [];
        $bk   = "lg";
        if( isset( $options['show'] ))         { $show = $options['show']; }
        if( isset( $options['form'] ))         { $form = $options['form']; }
        if( isset( $options['breakpoint'] ))   { $bk   = $options['breakpoint']; }
        if( isset( $options['columns_name']) ) { 
            $columns_name = $options['columns_name']; 
        } else {
            foreach( $columns as $column ) {
                $columns_name['$column'] = $column;
            }
        }
        return new HtmlString( view( 'helpers.myForm.index' )->with( 'rows' ,        $rows )
                                                             ->with( 'columns',      $columns )
                                                             ->with( 'columns_name', $columns_name )
                                                             ->with( 'show',         $show )
                                                             ->with( 'form',         $form )
                                                             ->with( 'bk',           $bk   )
                                                             ->render()
                                                             );
    }
    
    //　リスト型チェックボックス選択フォームを生成
    //
    static public function select( $options ) {
        
        //　値の初期化
        //
        $rows         = $options['rows'];    //　DB形式
        $columns      = $options['columns'];
        $show = [];
        $form = [];
        $bk   = "lg";
        if( isset( $options['show'] ))         { $show = $options['show']; }
        if( isset( $options['form'] ))         { $form = $options['form']; }
        if( isset( $options['breakpoint'] ))   { $bk   = $options['breakpoint']; }
        if( isset( $options['columns_name']) ) { 
            $columns_name = $options['columns_name']; 
        } else {
            foreach( $columns as $column ) {
                $columns_name['$column'] = $column;
            }
        }
        return new HtmlString( view( 'helpers.myForm.select' )->with( 'rows' ,        $rows )
                                                             ->with( 'columns',      $columns )
                                                             ->with( 'columns_name', $columns_name )
                                                             ->with( 'show',         $show )
                                                             ->with( 'form',         $form )
                                                             ->with( 'bk',           $bk   )
                                                             ->render()
                                                             );
    }
    
    // リスト型隠しフォームを生成
    //
    static public function hidden( $options ) {
        
    }
    
    // 入力フォームを生成
    //
    static public function input( $options ) {
        
        // dd( $options );
        //　値の初期化
        //
        $names          = $options['names'];  
        // typesの取りうる値
        // text, number, date, email, password, textarea, select, check, checkboxes, radio,
        //
        $types          = ( isset( $options['types']      )) ? $options['types']      : [] ; 
        $defaults       = ( isset( $options['defaults']   )) ? $options['defaults']   : [] ;
        $values         = ( isset( $options['values']     )) ? $options['values']     : [] ;
        $labels         = ( isset( $options['labels']     )) ? $options['labels']     : [] ;
        $classes        = ( isset( $options['classes']    )) ? $options['classes']    : []  ;
        $confirms       = ( isset( $options['confirms']   )) ? $options['confirms']   : []  ;
        $bk             = ( isset( $options['breakpoint'] )) ? $options['breakpoint'] : "md";
        $form_classes   = ( isset( $options['form_classes']  )) ? $options['form_classes'] : []  ;
        $label_classes  = ( isset( $options['label_classes'] )) ? $options['label_classes']: []  ;
        
        foreach( $names as $i => $name ) {
            if( ! isset( $types[$name] )) { $types[$name] = "text"; }
        }    
        
        return new HtmlString( view( 'helpers.myForm.input' )
                                            ->with( 'names',   $names  )
                                            ->with( 'types',   $types  )
                                            ->with( 'labels',  $labels )
                                            ->with( 'defaults',$defaults )
                                            ->with( 'values',  $values  )
                                            ->with( 'classes', $classes )
                                            ->with( 'confirms',$confirms )
                                            ->with( 'form_classes',  $form_classes )
                                            ->with( 'label_classes', $label_classes )
                                            ->with( 'bk',      $bk )
                                            ->render() );
    }
    

    // 新規登録フォームを生成
    //
    static public function create( $names, $header, $options = null ) {
        
        
    }
    
    // 変更フォームを生成
    //
    static public function edit( $names, $header, $options = null ) {
        
        
    }
    

}