@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('pagetitle',"Dashboard")

@section('pagecontent')
<!-- START PAGE CONTENT-->
<div class="page-content fade-in-up">        
</div>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/dashboard/dashboard.js') }}"></script>
@endsection    
