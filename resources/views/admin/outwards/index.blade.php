@php
    $counter = $first_item = intval($customer_orders->firstItem());
    $last_item = intval($customer_orders->lastItem());
    $total_records = $customer_orders->total();
@endphp

@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/dataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('pagetitle',$pagetitle)

@section('pagecontent')
<!-- Page Heading Breadcrumbs -->
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox mb-2">
        <div class="ibox-body py-3">
        <form action="{{ route('admin.outwards.index') }}" method="GET" id="form-filter" autocomplete="off">
            <div id="notify"></div>
            <div class="flexbox mb-2">
                <div class="col-lg-12 p-0">
                    <div class="d-flex flex-wrap pull-right">
                        <div class="mr-2 mb-2">
                            <label class="pr-1">Date Range:</label>
                            <div class="input-group date">
                                <input type="text" name="from" class="form-control datepicker" placeholder="From" value="{{ ($request->from ?? '')}}"/>
                                <span class="input-group-addon pl-2 pr-2">to</span>
                                <input type="text" name="to" class="form-control datepicker" placeholder="To" value="{{($request->to ?? '')}}" />
                            </div>
                        </div>
                        <div class="mr-2 mb-2">
                            <label class="pr-1">Customer:</label>
                           <select name="customer_id" class="form-control" onchange="formsubmit()">
                                <option value="">Select</option>
                            @foreach($customers->pluck('fullname','customer_id') as $id => $fullname)
                                <option value="{{ $id }}" @selected(request('customer_id') == $id)>{{ $fullname }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="mr-2 mb-2">
                            <label class="pr-1">Filter Records:</label>
                            <select name="f" class="form-control" onchange="formsubmit()">
                                <option value="" {{ request('f') == '' ? 'selected' : '' }}>Show All</option>
                                <option value="ac" {{ request('f') == 'ac' ? 'selected' : '' }}>Only Additional Charge</option>
                            </select>
                        </div>
                        <div class="mr-2 mb-2">                                
                            <label class="pr-1">Search:</label>
                            <div class="input-group-icon input-group-icon-left">
                                <span class="input-icon input-icon-right font-16"><i class="ti-search"></i></span>
                                <input type="text" name="s" value="{{ request('s') }}" class="form-control" placeholder="Search Serial Number">
                            </div>
                        </div>    
                        <div class="mb-2 align-self-end">
                            <button class="btn btn-primary" href="javascript:void(0)" onclick="formsubmit()">Filter</button>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
    <div class="ibox">
        <div class="ibox-body">
            <div class="flexbox mb-2">
                <div class="form-inline">
                    <label class="mb-0 mr-2">Show:</label>
                    <select name="p" class="form-control mr-2" onchange="formsubmit()">
                        <option value="50" @selected($show == '50')>50</option>
                        <option value="100" @selected($show == '100')>100</option>
                        <option value="150" @selected($show == '150')>150</option>
                        <option value="200" @selected($show == '200')>200</option>
                        <option value="300" @selected($show == '300')>300</option>
                    </select>                                                                           
                    <div>@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>
                </div>
                <div class="pull-right">                                                                        
                    <a class="btn btn-primary btn-rounded btn-air mb-2" href="{{ route('admin.outwards.create') }}">Add Outward</a> &nbsp;&nbsp;
                    <a class="btn btn-dark btn-rounded btn-air mb-2" onclick="printList('{{ route('admin.outwards.print',$request->all()) }}')" href="javascript:void(0)"><i class="fa fa-print"></i> Print </a>
                </div>
            </div>
        </form>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-default thead-lg">
                        <tr>
                            <th>#</th>
                            <th>Serial No.</th>
                            <th>Outward Date</th>
                            <th>Customer</th>
                            <th>Vehicle Number</th>  
                            <th>Delivered To</th>            
                            <th>Additional Charge</th>       
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                @if(isset($customer_orders) && $customer_orders->count()>0)
                    @foreach($customer_orders as $customer_order)
                        <?php
                            $customer = $customers->where("customer_id",$customer_order->customer_id)->first();
                        ?>
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $customer_order->sr_no }}</td>
                            <td>{{ $customer_order->date }}</td>
                            <td>{{ $customer->fullname }}</td>                            
                            <td>{{ $customer_order->vehicle}}</td>
                            <td>{{ $customer_order->delivery_address}}</td>
                            <td>{{ $customer_order->additional_charge }}</td>
                            <td>                                
                                <a href="{{ route('admin.outwards.edit',$customer_order->customer_order_id) }}" title="Edit"><button class="btn btn-outline-info btn-icon-only btn-sm mb-2"><i class="la la-pencil"></i></button></a> &nbsp;
                                <button class="btn btn-outline-danger btn-icon-only btn-sm mb-2" onclick="deleteCustomerOrder( '{{ route('admin.outwards.destroy',$customer_order->customer_order_id) }}','{{ $customer_order->customer_order_id }}' );" title="Delete"><i class="la la-trash"></i></button> &nbsp;                                                                
                                <a href="{{ route('admin.outwards.showReceipt',$customer_order->customer_order_id) }}" title="Receipt"><button class="btn btn-outline-dark btn-icon-only btn-sm mb-2"><i class="ti ti-printer"></i></button></a> &nbsp;                                  
                            </td>
                        </tr>
                    @endforeach                    
                @else
                        <tr>
                            <td colspan="12">
                                <div class="alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon"></i> No records found. </div>
                            </td>
                        </tr>        
                @endif
                    </tbody>
                </table>
                <div class="row">
                    <div class="col text-right">                    
                        <div><b>Sub Total: {{ Helper::formatAmount($customer_orders->sum('additional_charge')) }} &nbsp;&nbsp; Total Additional Charge: {{ Helper::formatAmount($total_additional_charge) }}</b></div>                    
                    </div>
                </div>
                <div class="flexbox mb-4 mt-4 noprint">
                    <div class="form-inline noprint">
                        <div>@includeIf('admin.inc.entries', ['first' => $first_item,'last' => $last_item,'total' => $total_records])</div>
                    </div>
                    <div class="flexbox noprint">
                        @if($request->all())
                            {!! $customer_orders->appends($request->all())->links() !!}
                        @else
                            {!! $customer_orders->links() !!}
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
    <script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('page-scripts')
	<script src="{{ asset('/assets/admin/js/outwards/index.js') }}"></script>
@endsection
