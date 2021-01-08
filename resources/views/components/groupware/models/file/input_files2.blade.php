@php

$route_to_upload_files = route( 'groupware.file.api.upload' );
$route_to_delete_file  = route( 'groupware.file.api.delete' );

@endphp
<style>
#droparea {
    border:2px dotted #0B85A1;
    width:500px;
    color:#92AAB0;
    text-align:left;vertical-align:middle;
    padding:10px 10px 10 10px;
    margin-bottom:10px;
    font-size:200%;
}

.progressBar {
    width: 200px;
    height: 22px;
    border: 1px solid #ddd;
    border-radius: 5px; 
    overflow: hidden;
    display:inline-block;
    margin:0px 10px 5px 5px;
    vertical-align:top;
}
  
.progressBar div {
    height: 100%;
    color: #fff;
    text-align: right;
    line-height: 22px; /* same as #progressBar height if we want text middle aligned */
    width: 0;
    background-color: #0ba1b5; border-radius: 3px; 
}
.statusbar {
    border-top:1px solid #A9CCD1;
    // min-height:25px;
    width:700px;
    padding:3px 3px 0px 3px;
    // vertical-align:top;
}
.statusbar:nth-child(odd){
    background:#EBEFF0;
}
.filename {
    display:inline-block;
    vertical-align:top;
    width:250px;
}
.filesize {
    display:inline-block;
    vertical-align:top;
    color:#30693D;
    width:100px;
    margin-left:10px;
    margin-right:5px;
}
.abort {
    background-color:#A8352F;
    -moz-border-radius:4px;
    -webkit-border-radius:4px;
    border-radius:4px;display:inline-block;
    color:#fff;
    font-family:arial;font-size:13px;font-weight:normal;
    padding:4px 15px;
    cursor:pointer;
    vertical-align:top
}
</style>

<div id='component_input_files_area'>
    <div id='attach_file_display'>
        @foreach( $files as $file ) 
            {{ Form::hidden( 'component_input_files[files][]', $file->id ) }}
            @if( in_array( $file->id, $attach_files ))
                {{ Form::hidden( $form_name.'[]', $file->id ) }}
                {{ $file->file_name }}<BR>
            @endif
        @endforeach
    </div>
    
    
    <button type='button' class='btn btn-outline-secondary' id='component_input_files_dialog_button'>添付ファイル編集</button>
</div>

<div class="m-1" id='component_input_files_dialog' title='添付ファイル編集'>
    <div>
        <div>
            @foreach( $files as $i => $file )
                @php
                    if( $i % 2 == 0) { $row = "even"; } else { $row = "odd"; };
                    $checked = ( in_array( $file->id, $attach_files )) ? 1 : 0;
                @endphp
                @if( $loop->first )
                    <div class="col-12">添付済みファイル</div>
                @endif
                <div class='statusbar {{ $row }}'>
                    <label for='file_{{ $file->id }}'>添付</label>
                    {{ Form::checkbox( $form_name.'[]', $file->id, $checked, [ 'id' => 'file_'.$file->id, 'class' => 'attach_files component_input_files_attach' ] ) }}
                    {{ Form::hidden( 'component_input_files[files][]', $file->id, ['class' => 'component_input_files_ids'] ) }} 
                    <div class='filename w-60 component_input_files_file_name' data-file_id='{{ $file->id }}'>{{ $file->id }} : {{ $file->file_name }}</div>
                    <div class='filesize w-35'                                 data-file_id='{{ $file->id }}'>{{ $file->user->dept->name }} : {{ $file->user->name }}</div>
                </div>
            @endforeach
            
            <div id='upload_files_list'></div>
            
            <div id="status1"></div>
            <div id="result"></div>
            
            <div class='col-12'></div>
            
            <div class='m-1 p-1'>
                <input type=file multiple class='form-control m-1' id='file_input'>
                <div id="droparea" clalss="m-2">Drop Files Here To Upload</div>
                <div id='component_input_files'></div>
            </div>
            <button type='button' class='btn btn-outline-secondary component_input_files_dialog_close_button'>閉じる</button>
        </div>
    </div>
</div>
    
    


<script>
/*
 *
 * モーダルウインドウの処理
 *
 */
let component_input_files_dialog = $('#component_input_files_dialog').dialog( {
        autoOpen: false,
        modal: true,
        width: 760,
    });
$('#component_input_files_dialog_button').on( 'click', function() { component_input_files_dialog.dialog( 'open' );  });

/*
 *
 * モーダルウインドウを閉じたら元フォームにペースト
 *
 */
$('.component_input_files_dialog_close_button').on( 'click', function() {
    var paste_target = $('#attach_file_display');
    var file_names = [];
    paste_target.html( "" );
    
    $('.component_input_files_file_name').each( function() {
        file_id = $(this).data('file_id');
        file_name = $(this).html();
        file_names[file_id] = file_name;
    });

    $('.component_input_files_attach:checked, .component_input_files_uploaded').each( function() {
        file_name = file_names[ $(this).val() ];
        paste_target.append( "<input type='hidden' name='{{ $form_name }}[]' value='" + $(this).val() + "'>" );
        paste_target.append( file_name + "<br>" );
    });
    $('.component_input_files_ids').each( function() {
        paste_target.append( "<input type='hidden' name='component_input_files[files][]' value='" + $(this).val() + "'>" );
    });
    component_input_files_dialog.dialog( 'close' ); 
});

/*
 *
 * ファイル入力フォームからのアップロード
 *
 */
$('#file_input').on( 'change', function( event ) {
    var files  = event.target.files;
    var object = $('#droparea');
    handleFileUpload( files,object );
});

/*
 *
 * ファイル削除ボタンの処理
 *
 */
$(document).on( "click", '.delete_button', function( event ) {

    console.log( 'DELETE BUTTON CLICK' );
    console.log( $(this).data('file_id') );

    var file_id = $(this).data('file_id');
    var object  = $(this).parent();
    console.log( file_id );
    
    
    deleteUploadedFile( file_id, object );
    //console.log( $(this).parent().remove() );
});

function deleteUploadedFile( file_id, object ) {
    var url  ="{{ $route_to_delete_file }}"; 
    console.log( 'DELETE FILE', file_id, object );

    var fd = new FormData();
    fd.append('file_id', file_id         );
    fd.append('user_id', {{ user_id() }} );
    fd.append('_token',  '{{ csrf_token() }}' );

    $.ajax({
        url: url,
        method: "POST",
        contentType:false,
        processData: false,
        cache: false,
        data: fd,
        //data: { 'user_id': {{ user_id() }}, 'file_id': file_id, '_token': '{{ csrf_token() }}' }

    }).done( function( data, status, xhr ) {
        console.log( data, status, xhr );
        object.remove();
    }).error( function( xhr, status, error ) {
        console.log( xhr, status, error );
        alert( 'エラーで削除できませんでした');
    }); 

}


/*
 *
 * ファイルのアップロード処理
 *
 */
function sendFileToServer(formData,statusbar) {
    console.log( formData, statusbar );
    var uploadURL ="{{ $route_to_upload_files }}"; //Upload URL
    var extraData ={}; //Extra Data.
    var jqXHR=$.ajax({
        xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                xhrobj.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //Set progress
                    statusbar.setProgress(percent);
                }, false);
            }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType:false,
        processData: false,
        cache: false,
        data: formData,
        success: function(data){
            statusbar.setProgress(100);
            // $("#status1").append("File upload Done<br>");
        }
    }).done( function( data, status, xhr ) {
        console.log( data );
        console.log( status );
        console.log( xhr );
        console.log( data.user_id, data.file_id );
        
        statusbar.file.val(     data.file_id );
        statusbar.checkbox.val( data.file_id );
        statusbar.filename.data( 'file_id', data.file_id );
        statusbar.size.data(     'file_id', data.file_id );
        statusbar.btn.data(      'file_id', data.file_id );

    }); 
    statusbar.setAbort(jqXHR);
}

function handleFileUpload(files,obj) {
   for (var i = 0; i < files.length; i++) {
        var fd = new FormData();
        fd.append('upload_file', files[i]);
        fd.append('user_id', {{ user_id() }} );
        fd.append('_token',  "{{ csrf_token() }}" );
  
        var status_bar = new createStatusbar(obj); //Using this we can set progress.
        status_bar.setFileNameSize(files[i].name,files[i].size);
        sendFileToServer( fd,status_bar );
   }
}

/*
 *
 * ステータスバーの処理
 *
 */
let rowCount=0;
function createStatusbar(obj) {
    rowCount++;
    var row="odd";
    if(rowCount %2 ==0) row ="even";
    
    this.statusbar   = $("<div class='statusbar "+row+"'></div>"                                                            );
    this.btn         = $("<div class='btn btn-danger delete_button m-1' data-file_id=''>削除</div>"                          ).appendTo(this.statusbar);
    this.checkbox    = $("<input type=hidden name='{{ $form_name }}[]' checked class='component_input_files_uploaded'>"     ).appendTo(this.statusbar);
    this.file        = $("<input type=hidden name='component_input_files[files][]'  class='component_input_files_ids'>"     ).appendTo(this.statusbar);
    this.filename    = $("<div class='filename component_input_files_file_name' data-file_id=''></div>"                      ).appendTo(this.statusbar);
    this.size        = $("<div class='filesize'                                 data-file_id=''></div>"                      ).appendTo(this.statusbar);
    this.progressBar = $("<div class='progressBar'><div></div></div>"                                                       ).appendTo(this.statusbar);
    this.abort       = $("<div class='abort'>Abort</div>"                                                                   ).appendTo(this.statusbar);
    
    $("#upload_files_list").after(this.statusbar);
    this.btn.hide();
    this.checkbox.hide();
  
    this.setFileNameSize = function(name,size) {
        var sizeStr="";
        var sizeKB = size/1024;
        if(parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB/1024;
            sizeStr = sizeMB.toFixed(2)+" MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2)+" KB";
        }
  
        this.filename.html(name);
        this.size.html(sizeStr);
    }
    
    this.setProgress = function(progress) {       
        var progressBarWidth =progress*this.progressBar.width()/ 100;  
        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
            this.abort.hide();
            this.btn.show();
            this.checkbox.show();
        }
    }
    
    this.setAbort = function(jqxhr) {
        var sb  = this.statusbar;
        this.abort.click(function() {
            jqxhr.abort();
            sb.hide();
        });
    }
    
    this.setDeleteButton = function( delete_button ) {
        var btn = this.delete_button;
        console.log( btn.data('fileid' ));
    }
}

$(document).ready( function() {

    $(".attach_files").checkboxradio( { icon: false } );
    $(".attach_files").checkboxradio( { mini: true } );

    let obj = $('#droparea');
    
    console.log( obj );

    obj.on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '2px solid #0B85A1');
    });
    
    obj.on('dragover', function (e) {
         e.stopPropagation();
         e.preventDefault();
    });
    obj.on('drop', function (e) {
         $(this).css('border', '2px dotted #0B85A1');
         e.preventDefault();
         var files = e.originalEvent.dataTransfer.files;
      
         //We need to send dropped files to Server
         handleFileUpload(files,obj);
    });

    //　ファイルがドロップエリアの外でドロップされると、ブラウザが別で開くので dropイベントを防ぐ処理
    //
    $(document).on('dragenter', function ( event ) {
        event.stopPropagation();
        event.preventDefault();
    });
    $(document).on('dragover', function (event) {
        event.stopPropagation();
        event.preventDefault();
        obj.css('border', '2px dotted #0B85A1');
    });
    $(document).on('drop', function ( event ) {
        event.stopPropagation();
        event.preventDefault();
    });
});
</script>