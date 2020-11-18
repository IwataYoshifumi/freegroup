@extends('layouts.app')

@php
use App\Models\Vacation\Application;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

$user = User::find( Auth::id() );

@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            @include( 'vacation.application.menu' )
            <div class="card">
                
                
                <div class="card-header">{{ config( Route::currentRouteName() ) }}</div>

                <div class="card-body">

                    @include( 'layouts.flash_message' )
                    @include( 'layouts.error' )
                    
                    <div class="border border-dark rounded d-none d-sm-block">
                        <h6 class="bg-success text-white w-100 p-2">申請者</h6>
                        <div class="row m-1 w-100">
                            <div class="col-5  col-sm-3 m-1 bg-light align-middle">{{ $user->department->name }}</div>
                            <div class="col-5  col-sm-3 m-1 bg-light align-middle">{{ $user->grade }}</div>
                            <div class="col-11 col-sm-5 m-1 bg-light align-middle">{{ $user->name }}</div>
                        </div>
                    </div>

                    <div class="border border-dark rounded mt-3">
                        <h6 class="bg-success text-white w-100 p-2">申請内容</h6>
                        <div class="p-1">
                            {{ Form::open( [ 'url' => route( 'vacation.application.index' ) ] ) }}
                            @csrf
                            @method('GET')
                            <input type='hidden' name='find[Search]' value='1'>
                            
                            <div class="row m-1">
                                <div class="form-group col-12 col-lg-5 row">
                                    <label for="off_date" class="col-4">休暇日</label>
                                    {{ Form::date( 'find[off_date]', ( isset( $find['off_date'] )) ? $find['off_date'] : "" , 
                                        ['class' => 'form-control col-8', 'id' => 'off_date' ]) }} 
                                </div>
                                
                                <div class="form-group col-12 col-lg-5 row">
                                    <label class="col-4">表示数</label>
                                    {{ Form::select( 'find[pagination]',[ 5 => 5,10=>10, 15=>15], ( isset( $find['pagination'] )) ? $find['pagination'] : 10, 
                                                 [ 'class' => 'form-control col-4', 'label' => '表示数' ] ) }}
                                </div>

                            </div>

                            <div class="row m-1">
                                  
                                  <a class="col-12 col-lg-4 btn btn-sm btn-outline" 
  	                                    data-toggle="collapse" 
  	                                    href="#collapseExample" 
  	                                    role="button" 
  	                                    aria-expanded="false" 
  	                                    aria-controls="collapseExample">
                                        <div class="col-12 border"><label>ステータス</label></div>
                                  </a>
                                
                                <div class="col-11 collapse row m-1 p-1 border border-dark" id="collapseExample">
                                @php
                                    $i = 0;
                                    $Status   = array( '承認待ち', '承認','休暇取得完了', '却下', '取り下げ'  ); 
                                    if( ! isset( $find['status'] )) { $find['status'] = array(); }
                                    
                                    foreach( $Status as $s ) {
                                        if( in_array( $s, $find['status'] )) {
                                            $checked = true;
                                        } else {
                                            $checked = false;
                                        }

                                        echo "<div class='col-12 col-lg-2'>";
                                        echo Form::checkbox( "find[status][$i]", $s, $checked  );
                                        echo "<span class='status'>$s</span></div>\n";
                                    
                                        $i++;
                                    }
                                @endphp
                                </div>
                                </div>
                                {{ Form::submit( '検索', [ 'class' => 'form-control btn btn-primary col-3 col-lg-2' ] ) }}

                            </div>
                        </div>
                        
                        {{ Form::close() }}

                        </div>
                        
                            
                            @php 
                                $statusClass = ['承認待ち'   => 'alert-primary font-weight-bold text-primary', 
                                                '承認'     => 'alert-success font-weight-bold',
                                                '却下'     => 'alert-danger font-weight-bold text-danger',
                                                '取り下げ' => 'alert-warning font-weight-bold',
                                                '休暇取得完了' => 'alert-success font-weight-bold' ];
                            @endphp
                        
                        
                        <div class='container table table-border bg-light m-1 w-100'>
                            
                            <thead>
                            <div class="row bg-dark text-white">
                                <div class="col-lg-2 d-none d-lg-block">ステータス</div>
                                <div class="col-lg-2 d-none d-lg-block">休暇種別</div>
                                <div class="col-lg-2 d-none d-lg-block">申請日</div>
                                <div class="col-lg-2 d-none d-lg-block">休暇期間</div>
                                <div class="col-lg-2 d-none d-lg-block">休暇日数</div>
                                <div class="col-lg-2 d-none d-lg-block">理由</div>
                            </div>
                            </thead>
                            
                            @foreach( $applications as $app )
                                <div class="row w-100">
                                    <a class='btn btn-outline-primary col-6 col-lg-2 {{ $statusClass[$app->status] }}' 
                                       href='{{ route( 'vacation.application.show', ['application'=> $app->id ] ) }}'>
                                        {{ $app->status     }}</a>
                                    <div class="col-12 d-lg-none"></div>
                                    <div class='col-4 d-lg-none'>休暇種別</div>
                                    <div class="col-8 col-lg-2">{{ $app->type }}</div>
                                    <div class='col-4 d-lg-none'>申請日</div>
                                    <div class="col-8 col-lg-2">{{ $app->date       }}</div>
                                    <div class='col-4 d-lg-none'>休暇期間</div>
                                    <div class="col-8 col-lg-2">
                                        {{ $app->print_period_for_index() }}
                                        </div>
                                    <div class='col-4 d-lg-none'>休暇日数</div>
                                    <div class="col-8 col-lg-2">{{ $app->print_num()   }}</div>
                                    <div class='col-4 d-lg-none'>休暇理由</div>
                                    <div class="col-8 col-lg-2">{{ $app->reason  }}</div>
                                </div>
                                <div class="col-12 border border-dark-sm"></div>
                            @endforeach

                            </div>
                            {{ $applications->links() }}
                            <script>
                            $('[data-toggle="tooltip"]').tooltip();
                            </script>
                        
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@php
#dump( Session::all() );
@endphp 

@endsection

