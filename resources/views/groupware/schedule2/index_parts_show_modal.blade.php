@php
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\Schedule;
use App\Models\Dept;

@endphp

<!-- Button trigger modal -->
<button type="button" class="btn btn-sm btn-outline-dark m-2 p-1" data-toggle="modal" data-target="#myModal">
  スケジュール検索
</button>

<!-- Modal -->
{{ Form::open( [ 'route' => Route::currentRouteName() ] ) }}
    @csrf
    @method( 'GET' )
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">スケジュール検索条件</h4>
          </div>
          <div class="modal-body">
              <div class="row">
                  <div class="col-12 col-md-4">検索条件</div>
                  <div class="col-12 col-md-7">
                      @php
                        $search_mode = ( isset( $request->search_mode )) ? $request->search_mode : 2;
                      @endphp
                      
                      {{ Form::select( 'search_mode', Schedule::get_array_for_search_mode(), $search_mode, [ 'class' => 'form-control' ] ) }}
                  </div>
                  
                
                
                  <div class="col-12 col-md-4">表示日</div>
                  <div class="col-12 col-md-7">
                      <input type="date" class="form-control" name="base_date" value="{{ $request->base_date }}">
                  </div>
                  <div class="col-12 col-md-4 mt-1">部署</div>
                  <div class="col-12 col-md-7 mt-1">
                    
                    
                    {{ Form::select( 'dept_id', Dept::getArrayforSelect(), $request->dept_id, [ 'class' => 'form-control' ] ) }}                                      
                      
                  </div>

                  <div class="col-12 col-md-4 mt-1">社員</div>
                  <div class="col-12 col-md-8 mt-1">
                    <!--- コンポーネント InputUsersComponent --->                                
                    <x-input_users :users="$users"/>
                  </div>
                  
                  <div class="col-12 col-md-4 mt-1">日報</div>
                  <div class="col-12 col-md-8 mt-1">
                    @php
                      $array_has_reports = [ '' => null, 1 => '日報あり', -1 => '日報なし' ];
                    @endphp
                    
                    {{ Form::select( 'has_reports', $array_has_reports, $request->has_reports, [ 'class' => 'form-control col-6' ] ) }}


                  </div>
                  

              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
            <button type="button" class="btn btn-search col-4" onClick="this.form.submit()">検索</button>
          </div>
        </div>
      </div>
    </div>
{{ Form::close() }}