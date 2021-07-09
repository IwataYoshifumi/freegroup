<?php

if (! function_exists('is_debug')) {
    function is_debug() {
        return config( 'app.debug' );
    }
}

if (! function_exists('if_debug')) {
    function if_debug( ...$values ) {
        if( is_debug() ) { dump( $values ); }
    }
}

if (! function_exists('if_debug_log')) {
    function if_debug_log( ...$values ) {
        if( is_debug() ) { Log::debug( $values ); }
    }
}

if (! function_exists('if_debug_dd')) {
    function if_debug_dd( ...$values ) {
        if( is_debug() ) { dd( $values ); }
    }
}

if (! function_exists('_print')) {
    function _print( ...$values ) {
        if( is_debug() ) { 
            if( is_array( $values )) {
                echo "<br>\r\n";
                foreach( $values as $i => $value ) {
                    if( is_array( $value )) { 
                        _print( $value ); 
                        
                    } else {
                        $print = $i . " => ". $value . "<br>\r\n";
                        echo $print;
                    }                 
                }
            } else {
                echo $value." : "; 
            }
        }
    }
}


if (! function_exists('op')) {
    function op( $value ) {
        return optional( $value );
    }
}

if (! function_exists('user_id')) {
    function user_id() {
        return auth('user')->id();
    }
}

if (! function_exists('admin_id')) {
    function admin_id() {
        return auth('admin')->id();
    }
}

if (! function_exists('customer_id')) {
    function customer_id() {
        return auth('customer')->id();
    }
}

if (! function_exists('guard')) {
    function guard() {
        return auth()->guard();
    }
}

if (! function_exists('user')) {
    function user() {
        return optional( auth('user'))->user();
    }
}

if (! function_exists('admin')) {
    function admin() {
        return optional( auth('admin'))->user();
    }
}

if (! function_exists('customer')) {
    function customer() {
        return auth('customer')->user();
    }
}


//　フォーム用の配列を作成するヘルパー( キーがIDになる )
//
if (! function_exists('toArray')) {
    function toArray( $objects, $name = 'name', $id = 'id' ) {
        $array = [];
        foreach( $objects as $object ) {
            $array[ $object->$id ] = $object->$name;
        }
        return $array;
    }
}
if (! function_exists('toArrayWithEmpty')) {
    function toArrayWithEmpty( $objects, $name = 'name', $id = 'id' ) {
        $array = [ '' => '' ];
        foreach( $objects as $object ) {
            $array[ $object->$id ] = $object->$name;

        }
        return $array;
    }
}
if (! function_exists('toArrayWithNull')) {
    function toArrayWithNull( $objects, $name = 'name', $id = 'id' ) {
        return toArrayWithEmpty( $objects, $name, $id );
    }
}


//　フォーム用の配列を作成するヘルパー( キーは自動インクリメント )
//
if (! function_exists('toArrayKeyIncremental')) {
    function toArrayKeyIncremental( $objects, $name = 'name' ) {
        $array = [];
        $i = 0;
        foreach( $objects as $object ) {
            $array[ $i ] = $object->$name;
            $i++;
        }
        return $array;
    }
}

if (! function_exists('toArrayKeyIncrementalWithEmpry')) {
    function toArrayKeyIncrementalWithEmpry( $objects, $name = 'name' ) {
        $array = [ '' => '' ];
        $i = 0;
        foreach( $objects as $object ) {
            $array[ $i ] = $object->$name;
            $i++;
        }
        return $array;
    }
}

if (! function_exists('toArrayKeyIncrementalWithNull')) {
    function toArrayKeyIncrementalWithNull( $objects, $name = 'name' ) {
        return toArrayKeyIncrementalWithEmpry( $objects, $name );
    }
}

if (! function_exists('p_day_of_week')) {
    function p_day_of_week( DateTime $date ) {
        return config( 'constant.days_of_week' )[ $date->dayOfWeek ];
    }
}

//　日本語の漢字で曜日を表示する関数( 引数　曜日数値　０日曜日、６　土曜日)
//
if( ! function_exists( 'p_date_jp' )) {
    function p_date_jp( $w ) {
        switch( $w ) {
            case 0: return "日"; 
            case 1: return "月";
            case 2: return "火";
            case 3: return "水";
            case 4: return "木";
            case 5: return "金";
            case 6: return "土";
        }
        return "";        
    }
    
}
