@extends('errors.layout.layout') 

@section('pagetitle')
    Error 500
@endsection

@section('page-css')
<style>
    body {
        background-color: #fff;
        background-repeat: no-repeat;
        background-image: url(/assets/admin/img/icons/server-error-2.svg);
        background-position: 80% 10px;
    }
    .error-content {
        max-width: 620px;
        margin: 200px auto 0;
    }
    .error-icon {
        height: 160px;
        width: 160px;
        background-image: url(/assets/admin/img/icons/server-error.svg);
        background-size: cover;
        background-repeat: no-repeat;
        margin-right: 80px;
    }
    .error-code {
        font-size: 120px;
        color: #5c6bc0;
    }
</style>
@endsection

@section('page-content')
<span class="error-icon"></span>
<div class="flex-1">
    <h1 class="error-code">500</h1>
    <h3 class="font-strong">Internal Server Error !</h3>
    <p class="mb-4">We apologize for any inconvenience, but an unexpected error occurred while you were browsing our site.</p>
    <div>
        <a class="btn btn-primary btn-round" href="{{ route('home') }}">GO TO HOME PAGE</a>
    </div>
</div>    
@endsection 