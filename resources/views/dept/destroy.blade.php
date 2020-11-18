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
                <div class="card-header">部署　削除</div>
                
                <div class='bg-warning m-3'>部署を削除します。復帰できません。よろしければ「削除　実行」ボタンを押してください。</div>

                <div class="card-body">
                        <form method="POST" action="{{ route('dept.destoryed', ['dept' => $dept->id ] ) }}">
                        {{ Form::hidden( 'id', $dept->id ) }}
                        @csrf

                        <div class="form-group row">
                            <label for="dept_id" class="col-md-4 col-form-label text-md-right font-weight-bold">部署名</label>

                            <div class="col-md-6">
                                {{ $dept->name }}
                                

                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-danger">部署　削除</button>
                            {{ BackButton::form() }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
