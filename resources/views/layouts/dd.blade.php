@extends('layouts.app')

@php

use App\User;
use App\Http\Helpers\BackButton;

@endphp

@section('content')
    <div class="container">
        <H1>デバッグ　ビュー</H1>
        <div class="container">
            {{ if_debug( request() ) }}
        </div>
    </div>
@endsection

