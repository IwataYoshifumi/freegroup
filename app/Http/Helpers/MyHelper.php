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
    function if_debug_log( $value ) {
        if( is_debug() ) { Log::debug( $value ); }
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



// if (! function_exists('check_model')) {
    
//     function check_model( $model ) {
        
//         if( is_numeric( $model )) {
//             if( empty( $class )) { throw new Exception( __METHOD__ ); }
    
//             $this->id    = $model;
//             $this->class = $class;
//             $this->model = $class::find[ $this->id ];

//         } elseif( is_object( $model )) {
//             $class_name = get_class( $model );
//             if( empty( $class_name )) { throw new Exception( __METHOD__); }
//             if( empty( $class )) {
//                 $this->class = $class;
//             } elseif( $class_name != $class ) {
//                 throw new Exception( __METHOD__ ) ;
//             }
            
//             $this->id    = $model->id;
//             $this->model = $model;
//             $this->class = $class;
//         }

//     }
// }


// if( ! class_exists( 'check_model' ) ) {
//     // dump( 'check_model');
//     class check_model {
    
//         public $id;
//         public $model;
//         public $class;
    
//         public function __construct( $model, $class = null ) {
        
//             if( is_numeric( $model )) {
//                 if( empty( $class )) { throw new Exception( __METHOD__ ); }

//                 $this->id    = $model;
//                 $this->class = $class;
//                 $this->model = $class::find[ $this->id ];

//             } elseif( is_object( $model )) {
//                 $class_name = get_class( $model );
//                 if( empty( $class_name )) { throw new Exception( __METHOD__); }
//                 if( empty( $class )) {
//                     $this->class = $class;
//                 } elseif( $class_name != $class ) {
//                     throw new Exception( __METHOD__ ) ;
//                 }
                
//                 $this->id    = $model->id;
//                 $this->model = $model;
//                 $this->class = $class;
//             }
            
//         }

//         public function id() {
//             return $this->id;
//         }
        
//         public function model() {
//             return $this->model;
//         }
        
//         public function class_name() {
//             return $this->class;
//         }
            
        
//     }
// }