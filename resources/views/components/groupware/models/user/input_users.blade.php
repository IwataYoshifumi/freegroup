@php
    use App\myHttp\GroupWare\Models\User;
    use App\myHttp\GroupWare\Models\Dept;

@endphp

<div class="clearfix">
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <div class="row">
        <div id="user_ids_form" class="font-weight-bold col-12">
            @php
                #dump( old( 'users' ), $users );
                
                #if( is_array( $users )) {
                #    $users = User::find( $users );
                #} 

            @endphp
            @foreach( $users as $c ) 
                <div class='col user_id' id='user_id_{{ $c->id }}' data-user_id='{{ $c->id }}'>
                    <div class='btn btn-sm btn-outline-secondary' onClick='delete_user_id( {{ $c->id }} )'>-</div>
                    <input type=hidden name='users[]' value={{ $c->id }}>{{ $c->name }}
                </div>
            @endforeach
        </div>
        <div class="col-12 m-1"></div>
        
        <div class="col-10 btn-group">
            {{ Form::select( 'component_input_users_dept_id', Dept::getArrayforSelect(), old( 'dept_id' ),   [ 'class' => 'form-control', 'id' => 'dept_select', ] ) }}
            {{ Form::text( 'component_input_users_user_name', old( 'component_input_users_user_name' ), [ 'class' => 'form-control', 'id' => 'search_users', 'placeholder' => '名前' ] ) }}
        </div>
        <div class="btn btn-sm btn-outline-secondary col-1" onClick='clear_search_users()'>x</div>
        <div id="user_lists" class="bg-light">
            <div class="col schedule">1</div>
        </div>
    </div>
</div>

<script language='JavaScript'>
    //　検索クリアーボタン
    //
    function clear_search_users() {
        $('#search_users').val( null );
        $('#dept_select').val( null );
        $('#search_users').change();
    }

    //　顧客ID追加
    //
    function user_id_click( id, name ) {
        // console.log( 'aa', id );
        try {
            $('.user_id').each( function() {
                console.log( $(this).data('user_id') ); 
                if( id === $(this).data('user_id') ) {
                    // console.log( 'duplicate');
                    throw new Error('duplicate id');
                }
            });
            
            var form = $('#user_ids_form');
            var tag = "<div class='col user_id' id='user_id_" + id + "' data-user_id=" + id +">";
            tag    += "     <div class='btn btn-sm btn-outline-secondary'";
            tag    += "          onClick='delete_user_id(" + id + ")'>-</div>";
            tag    += "     <input type=hidden name='users[]' value=" + id +">"+ name;
            tag    += "</div>";
            form.append( tag );
            // console.log( id );
        } catch( e ) {
            console.log( 'user_id_click duplicate ID');
        }
    };
    //　顧客ID削除ボタン
    //
    function delete_user_id( id ) {
        console.log( id  );
        var elm = '#user_id_' + id;
        console.log( $( elm ) );
        $( elm ).remove();
    };
    
    
    // $('.custmoer_ids').click( function() {
    //     console.log( $(this) ); 
    // });

    // 顧客検索フォーム
    //
    $('#dept_select').change( function() {
        $('#search_users').change();
    });
    
    $('#search_users').change( function() {
        var search = $(this).val();
        var dept_id = $('#dept_select').val();
        var url    = "{{ route( 'user.json.search' ) }}";
        console.log( search, dept_id );

        if( search || dept_id ) { 
            console.log( 'NOT NULL');
            $.ajax( url, {
                ttype: 'get',
                data:  { name : search, dept_id : dept_id },
                dataType: 'json',
            }).done( function( data ) {
                console.log( data );
                $("#user_lists").children().remove();
                $.each( data, function( i, val ) {
                    // var tag = "<div class='btn btn-sm btn-outline-secondary'>+</div>";
                    var tag = "<div class='col schedule user_ids'";
                    tag += "         id=user_id" + val.id;
                    tag += "         data-user_id=" + val.id;
                    tag += "         value=" + val.id;
                    tag += "        >";
                    tag += "    <div class='btn btn-sm btn-outline-secondary'";
                    tag += "         onClick='user_id_click(" + val.id + ",\"" + val.name + "\")'";
                    tag += "    >+</div>";
                    tag += "    【" + val.dept_name + "】" + val.name + " " + val.grade;
                    tag += "</div>";
                    // console.log( tag, name );
                    $("#user_lists").append( tag ); 
                });
            });   
        } else {
            console.log( 'NULL' );
            $("#user_lists").children().remove();
        }
    });
        
    $(document).ready( function() {
        $('#search_users').change();
        
    });
        
</script>
