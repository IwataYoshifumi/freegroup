@extends('layouts.app')

@php

use App\Models\Vacation\Dept;

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'vacation.dept.menu' )
            <div class="card">

                <div class="card-header">部署名　変更</div>
                <div class="card-body">
                        <form method="POST" action="{{ route('vacation.dept.update', ['dept' => $dept->id ] ) }}">
                        {{ Form::hidden( 'id', $dept->id ) }}
                        @csrf

                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">部署名</label>

                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name', $dept->name ) , ['class' => 'form-control' , 'required' ] ) }}
                                
                                @foreach( $errors->all() as $error ) 
                                    <span class="text-danger">{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">部署名 変更</button>
                            <a href='{{ route( 'vacation.dept.index' ) }}' class='btn btn-outline-secondary'>戻る</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
