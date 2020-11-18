@extends('layouts.app')

@php

use App\Models\Dept;
use App\Http\Helpers\BackButton;

@endphp

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include( 'dept.menu' )
            <div class="card">
                <div class="card-header">部署　新規登録</div>

                <div class="card-body">
                        <form method="POST" action="{{ route('dept.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right">部署名</label>

                            <div class="col-md-6">
                                {{ Form::text( 'name', old( 'name' ), ['class' => 'form-control', 'required' ] ) }}
                                
                                @foreach( $errors->all() as $error ) 
                                    <span class="text-danger">{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">登録</button>
                            {{ BackButton::form() }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
            @foreach ($errors->all() as $error )
                <li>{{ $error }}</li>
            @endforeach
@endsection
