<?php 

namespace App\Http\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OutputCSV {
    
    // 配列をCSVファイルを出力する
    //
    static public function input_array( $options ) {

        // 引数の初期化
        //
        $file_name   = "csv_file.csv";
        $column_name = []; 
        $lists = $options['lists'];
        if( isset( $options['file_name']   )) { $file_name   = $options['file_name'];   }
        if( isset( $options['column_name'] )) { $column_name = $options['column_name']; }

        //　ファイル作成
        //
        $file = fopen( 'php://memory', 'w+' );
        fputcsv( $file, $column_name );
        foreach( $lists as $row ) {
            // dd( $row );
            fputcsv( $file, $row );
        }
        rewind( $file );
        $csv = str_replace( PHP_EOL, "\r\n", stream_get_contents( $file ));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $headers = array(
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$file_name.'"',
            );
        fclose( $file );
        return Response::make( $csv, 200, $headers );
    }
    
    // DBモデルをCSV出力する
    //
    static public function input_eloquent( $options ) {

        // 引数の初期化
        //
        $file_name   = "csv_file.csv";
        $column_name = []; 
        $lists = $options['lists'];
        if( isset( $options['file_name']   )) { $file_name   = $options['file_name'];   }
        if( isset( $options['column_name'] )) { $column_name = $options['column_name']; }
        
        
        $file = fopen( 'php://memory', 'w+' );
        foreach( $lists as $row ) {
            fputcsv( $file, $row->toArray() );
        }
        rewind( $file );
        $csv = str_replace( PHP_EOL, "\r\n", stream_get_contents( $file ));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $headers = array(
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$file_name.'"',
            );
        fclose( $file );
        return Response::make( $csv, 200, $headers );
    }
    
    // CSV出力用のフォームを作成する
    //
    static public function button( $options ) {
        
        //　引数の初期化
        //
        $method     = "GET";
        $class      = "btn btn-warning";
        $operator   = [];
        $route_name = $options['route_name'];
        $inputs     = $options['inputs'];
        
        if( isset( $options['method'] )) { $method = $options['method']; }
        if( isset( $options['class']  )) { $method = $options['class'];  }
        
        return new HtmlString( view( 'helpers.outputCSV.button' )->with( 'route_name' , $route_name )
                                                                 ->with( 'inputs',      $inputs )
                                                                 ->with( 'method',      $method )
                                                                 ->with( 'class',       $class  )
                                                                 ->render()
                                                                );
    }
    
}