@php
    $counter = $first_item = intval($users->firstItem());
    $last_item = intval($users->lastItem());
    $total_records = $users->total();
@endphp

@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/dataTables/datatables.min.css') }}">
@endsection

@section('pagetitle',$pagetitle)

@section('pagecontent')
<!-- Page Heading Breadcrumbs -->
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-body">
            <div id="notify"></div>
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="mb-3">@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>
                <div class="d-flex flex-wrap">
                    <div class="flex-grow mb-2">
                        <form method="GET" action="{{ route('admin.admins') }}" id="form-filter">
                            <div class="input-group-icon input-group-icon-left mr-3">
                                <span class="input-icon input-icon-right font-16"><i class="ti-search"></i></span>
                                <input type="text" name="keyword" value="{{($request->keyword) ?? '' }}" class="form-control" placeholder="Search">			
                            </div>
                        </form>
                    </div>    
                    <div class="flex-grow text-right">
                        <a class="btn btn-rounded btn-primary btn-air" href="{{ route('admin.admins.create') }}">Add Admin</a>
                    </div>                        
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-default thead-lg">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>User Name</th>
                            <th>Phone</th>
                            <th>Enable</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                @if(isset($users) && $users->count()>0)
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td><img src="{{ $user->photo }}" class="img-circle" alt="customer" height="40" width="40"> &nbsp;  {{ $user->fullname }}</td>
                            <td>{{$user->username}}</td>
                            <td>{{ $user->email }}</td>
                            <td>@includeIf('admin.inc.status', ['status' => $user->isactive])</td>
                            <td>
                                <a href="{{ route('admin.admins.edit',$user->user_id) }}" title="Edit">
                                    <button class="btn btn-outline-info btn-sm mb-1"><i class="la la-pencil"></i> Edit </button>
                                </a> &nbsp;
                                <button class="btn btn-outline-danger btn-sm mb-1" onclick="deleteUser( '{{ route('admin.admins.destroy',$user->user_id) }}','{{ $user->user_id }}' );" title="Delete">
                                    <i class="la la-trash"></i> Delete 
                                </button> &nbsp;
                            </td>
                        </tr>
                    @endforeach
                @else
                        <tr>
                            <td colspan="12"><div class="alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon"></i> No records found. </div></td>
                        </tr>
                @endif
                    </tbody>
                </table>
                <div class="flexbox mb-4 mt-4 noprint">
                    <div class="form-inline noprint">                        
                        <div>@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>
                    </div>
                    <div class="flexbox noprint">
                        @if($request->filled('keyword'))
                            {!! $users->appends(['keyword'=>$request->keyword])->links() !!}
                        @else
                            {!! $users->links() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('plugin-scripts')
	<script src="{{ asset('/assets/app/vendors/dataTables/datatables.min.js') }}"></script>
@endsection

@section('page-scripts')
	<script src="{{ asset('/assets/admin/js/user/index.js') }}"></script>
@endsection
