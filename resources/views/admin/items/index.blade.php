@php
    $counter=$first_item=intval($items->firstItem());
    $last_item=intval($items->lastItem());
    $total_records=$items->total();
@endphp
@extends('admin.layouts.app')
@section('pagetitle',$pagetitle)

@section('pagecontent')
    <!-- Page Heading Breadcrumbs -->
    @include('admin.layouts.breadcrumbs')
    <div class="page-content fade-in-up">
        <div class="ibox">
            <div class="ibox-body">
                <div id="notify"></div>
                <form action="{{ route('admin.items.index') }}" method="get" id="form-filter">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="mb-3">@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>                            
                        <div class="d-flex flex-wrap">
                            <div class="form-inline flex-grow mb-2">
                                <label class="mb-0 mr-2">Show:</label>
                                <select name="show" class="form-control mr-2 mb-2" onchange="formsubmit()">
                                    <option value="25" {{ $show == '25' ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $show == '50' ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $show == '100' ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                            <div class="flex-grow mb-2">
                                <div class="input-group-icon input-group-icon-left mr-3">
                                    <span class="input-icon input-icon-right font-16"><i class="ti-search"></i></span>
                                    <input type="text" name="keyword" value="{{($request->search) ?? '' }}" class="form-control" placeholder="Search">			
                                </div>                        
                            </div>    
                            <div class="flex-grow text-right">
                                <a class="btn btn-rounded btn-primary btn-air" href="{{ route('admin.items.create') }}">Add Item</a>                        
                            </div>    
                        </div>                
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover" id="customers-table">
                        <thead class="thead-default thead-lg">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Enable</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                    @if(isset($items) && $items->count()>0)
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $counter++}}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>@includeIf('admin.inc.status', ['status' => $item->isactive])</td>
                                <td>
                                    <a href="{{ route('admin.items.edit',$item->item_id) }}" class="btn btn-sm btn-outline-info mb-1">
                                        <span class="btn-icon"><i class="la la-pencil"></i>Edit</span>
                                    </a>&nbsp;&nbsp;
                                    <button class="btn btn-sm btn-outline-danger mb-1" onclick="deleteItem('{{ route('admin.items.destroy',$item->item_id) }}');">
                                        <span class="btn-icon"><i class="la la-trash"></i>Delete</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                            <tr>
                                <td colspan="12"><div class="alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon"></i> No records found. </div></td>
                            <tr>
                    @endif
                        </tbody>
                    </table>
                    <div class="flexbox mb-4 mt-4 noprint">
                        <div class="form-inline noprint">                        
                            <div>@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>
                        </div>
                        <div class="flexbox noprint">
                            @if($request->filled('keyword'))
                                {!! $items->appends(['keyword'=>$request->keyword])->links() !!}
                            @else
                                {!! $items->links() !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
	<script src="{{ asset('/assets/admin/js/items/index.js') }}"></script>
@endsection
