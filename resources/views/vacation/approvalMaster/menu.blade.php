<div class="m-2">
    <div class='row'>
    <a class='btn btn-primary col-3 m-1' id='index'      href='{{ route('vacation.approvalMaster.index')         }}'>承認マスター【一覧】</a>
    <a class='btn btn-primary col-3 m-1' id='indexUsers' href='{{ route('vacation.approvalMaster.indexUsers')    }}'>承認マスター【割当状況】</a>
    </div><div class='row'>
    <a class='btn btn-success col-3 m-1' id='create'     href='{{ route('vacation.approvalMaster.create')        }}'>承認マスター【新規作成】</a>
    <a class='btn btn-success col-3 m-1' id='allocate'   href='{{ route('vacation.approvalMaster.selectUsers' )  }}'>承認マスター【割当】</a>
    <a class='btn btn-success col-2 m-1' id='deallocate' href='{{ route('vacation.approvalMaster.deallocateSelectUsers' )  }}'>【割当解除】</a>
    </div>
</div>
