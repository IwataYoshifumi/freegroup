@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

$component_name = "select_user_component";
 
$form_name_of_select_dept  = "select_dept_". $form_name;
$form_name_of_search_user  = "search_user_". $form_name;

$class_name_of_select_dept = $component_name ."_select_dept";
$class_name_of_search_user = $component_name ."_search_user";
$class_name_of_select_user = $component_name ."_select_user";

$id_of_select_dept = $component_name ."_select_dept_".$index;
$id_of_search_user = $component_name ."_search_user_".$index;
$id_of_select_user = $component_name ."_select_user_".$index;

$user_selector = "#". $component_name . "_select_user_";



@endphp

<div class="clearfix">

    {{ Form::select( 
        $form_name_of_select_dept,
        $depts,
        old( $form_name_of_select_dept, $dept_id ), 
        [ 
            'class'      => 'form-control '. $class_name_of_select_dept, 
            'id'         => $id_of_select_dept, 
            'data-index' => $index, 
            // 'onChange()' => 'dept_select( $(this) );',
        ] 
        )}}
    
    {{ Form::text( 
        $form_name_of_search_user,
        old( $form_name_of_search_user, $user_name ), 
        [ 
            'class'      => 'form-control '. $class_name_of_search_user, 
            'id'         => $id_of_search_user, 
            'data-index' => $index, 
        ] 
        )}}
    
    {{ Form::select( 
        $form_name, 
        $users, 
        # old( $form_name, $user_id ), 
        $user_id, 
        [ 
            'class'      => 'form-control '. $class_name_of_select_user, 
            'id'         => $id_of_select_user, 
            'data-index' => $index, 
        ] 
        )}}

</div>
{{-------------------------------------------------------------------------------------

本コンポーネントを呼び出している blade で下記を最後に記述して、スクリプトを呼び出すこと

@stack('select_user_component_javascript')

-----------------------------------------------------------------------------------------}}
@push( 'select_user_component_javascript' )
    @once
        @php
            $dept_select_id = "#". $component_name ."_select_dept_";
            $name_search_id = "#". $component_name ."_search_user_";
        @endphp

        <script>
            /*
            This scripts are called by select_user_component.
            */
        
            $('.{{ $class_name_of_select_dept }}').change( function() {
                search_users( $(this) );
            });
            $('.{{ $class_name_of_search_user }}').change( function() {
                search_users( $(this) );
            });

        
            function search_users( obj ) {
                var i = obj.data('index');
                
                // var dept_id = obj.val();
                
                var dept_id = $('{{ $dept_select_id }}'+i).val();
                var name    = $('{{ $name_search_id }}'+i).val();
                
                console.log( i, dept_id,name );
                
                // var url = "{{ route( 'groupware.json.getUsersBlongsTo' ) }}";
                var url = "{{ route( 'groupware.json.getUsers' ) }}";
                
                $.ajax( url, {
                    ttype: 'get',
                    data: { dept_id : dept_id, name : name },
                    dataType: 'json',
                    
                }).done( function( data ) {
                    console.log( data );            
                    $('{{ $user_selector }}'+i).children().remove();
                    $('{{ $user_selector }}'+i).append($("<option>").val("").text("---"));
                    
                    $.each( data, function( id, name ) {
                        console.log( id, name );
                        //    $('{{ $user_selector }}'+i).append($("<option>").val(id).text(name).prop("selected", true));
                        $('{{ $user_selector }}'+i).append($("<option>").val(id).text(name));
                    });
                });
            }
        </script>
    @endonce
@endpush
