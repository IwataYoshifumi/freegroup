<div class="row m-2">
    <a class='btn btn-success col-4 col-lg-2 m-1' id='select_button'      
        href='{{ route( 'vacation.approval.select', ['user' => Auth::user()->id, ] )    }}'>承認業務</a>
    <a class='btn btn-primary col-4 col-lg-2 m-1' id='index_button' 
        href='{{ route( 'vacation.approval.index', [ 'user' => Auth::user()->id, ] )   }}'>承認一覧</a> 
</div>