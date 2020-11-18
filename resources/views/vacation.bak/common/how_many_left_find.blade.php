@php
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

@endphp

{{ Form::open( ['url' => url()->current(), 'method' => 'get', 'id' => 'index_form' ] ) }}
{{ Form::hidden( 'SearchQuery', 1 ) }} 

<table class="table bg-light border-dark align-middle p-1">
    <tr class=" align-middle" >
        <th class="align-middle">社員番号</th>
        <th class="align-middle">名前</th>
        <th class="align-middle">メール</th>
    </tr>

    <tr class="align-middle" >
        @php
            $depts = Dept::getArrayforSelect();
            $grades= User::getArrayForGradeSelcetForm();
        @endphp
        <td>{{ Form::text( 'find[code]', old( 'find[code]', ( isset( $find['code'] )) ? $find['code'] : "" ), 
                ['class' => 'form-control w-5', 'placeholder' => '社員番号' ] ) }}
        </td>
        <td>{{ Form::text( 'find[name]', old( 'find[name]', ( isset( $find['name'] )) ? $find['name'] : "" ), 
                ['class' => 'form-control w-15', 'placeholder' => '名前' ] ) }}
        </td>
        <td>{{ Form::text( 'find[email]', old( 'find[email]', ( isset( $find['email'] )) ? $find['email'] : null  ), 
                ['class' => 'form-control w-15', ] ) }}
        </td>

    </tr>
    <tr>
        <th class="align-middle">部署</th>
        <th class="align-middle">役職</th>
        <th class="align-middle">表示数</th></th>
    </tr>
    <tr>
        <td>{{ Form::select( 'find[dept_id]', $depts, old( 'find[dept_id]', ( isset( $find['dept_id'] )) ? $find['dept_id'] : null ),
                ['class' => 'form-control' ] ) }}
        </td>
        <td>{{ Form::select( 'find[grade]', $grades, old( 'find[grade]', ( isset( $find['grade'] )) ? $find['grade'] : null ),
                ['class' => 'form-control' ] ) }}</td>

        <td>{{ Form::select( 'find[pagination]', [ 10 => 10, 20 => 20, 30 => 30, 50 => 50, 100 => 100 ] ,
                                old( 'find[pagination]', ( isset( $find['pagination'] )) ? $find['pagination'] : ""  ),
                                    ['class' => 'form-control' ] )  }}</<td>
    </tr>
    
    <tr>
        <th colspan=3>
            <a class="btn btn-primary text-white" onClick="onClickSubmit()">検索</a>            
            <script>
                function onClickSubmit() {
                    $('#index_form').submit();
                }
                
            </script>
        </th>
    </tr>
</table>
{{ Form::close() }}