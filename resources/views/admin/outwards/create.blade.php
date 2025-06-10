@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/inwards/create.css') }}">    
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/outwards/type-ahead.css') }}">    
@endsection

@section('pagecontent')
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
@if(isset($customer_order))
    <form method="POST" action="{{ route('admin.outwards.update', $customer_order->customer_order_id) }}" id="customerorders-form" onsubmit="return OnFormSubmit(this, true)">
        @method('PATCH')
@else
    <form method="POST" action="{{ route('admin.outwards.store') }}" id="customerorders-form" onsubmit="return OnFormSubmit(this, false)">
@endif
    @csrf
        <div class="ibox ibox-fullheight">
            <div class="ibox-body">
                <input type="hidden" name="printoutward" value="1" id="printoutward">
                <div id="notify"></div>                
                <div class="alert alert-primary alert-bordered"><h5>Outward Info</h5></div>   
                <div class="row">
                    <div class="form-group col-lg-4 col-md-6 col-12">                            
                        <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-control customer_id" onchange="ChangeAddress(this)" required>
                            <option value="">Select</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->customer_id }}" @selected( (isset($customer_order) && $customer_order->customer_id == $customer->customer_id) )>
                                    {{ $customer->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('customer_id'))
                            <small class="error">{{ $errors->first('customer_id') }}</small>
                        @endif
                    </div>    
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">
                        <label for="date">Outward Date</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="date" id="order-date" class="form-control datepicker" placeholder="DD/MM/YYYY" value="{{ isset($customer_order) ? $customer_order->date : \Helper::DateFormat(today(), config('constant.DATE_FORMAT_SHORT')) }}" required>
                        </div>    
                        @if ($errors->has('date'))
                            <small class="error">{{ $errors->first('date') }}</small>
                        @endif
                    </div>
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">                            
                        <label for="sr_no">Serial Number</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="ti ti-receipt"></i></span>
                            <input type="number" name="sr_no" class="form-control" placeholder="Serial Number" value="{{ $customer_order->sr_no ?? '' }}" required>
                        </div>                                                                                                                                      
                    </div>                                                                                                                                                                                              
                </div>                 
                <div class="row">      
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">                            
                        <label for="address">Delivery Address</label>
                        <textarea name="address" class="form-control address" placeholder="Delivery Address" rows="2">{{ ($customer_order->address) ?? '' }}</textarea>
                    </div>                                                                                                                                  
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">                            
                        <label for="order_by">Order By</label>
                        <input type="text" name="order_by" class="form-control" placeholder="Order By" value="{{ $customer_order->order_by ?? '' }}">
                    </div>                                                                                                                    
                    <div class="form-group col-lg-4 col-md-6 col-sm-12">                            
                        <label for="vehicle">Vehicle Number</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                            <input type="text" name="vehicle" class="form-control" placeholder="Vehicle Number" value="{{ $customer_order->vehicle ?? '' }}">
                        </div>                                                                                                                                      
                    </div>                                                                                                                                              
                </div>            
                <br/><br/>
                <div class="alert alert-primary alert-bordered"><h5>Search Inward</h5></div>            
                <div class="row">                                  
                    <div class="col-lg-12 col-md-12 col-sm-12 mb-2">                    
                        <div class="d-flex align-items-center flexwrap">
                            <div class="mr-2 mb-2 flex-grow">
                                <label class="pr-1">Date Range:</label>
                                <div class="input-group date">
                                    <input type="text" name="from" id="from" class="from form-control datepicker" placeholder="From" value="{{ isset($from) ? \Helper::DateFormat($from, config('constant.DATE_FORMAT_SHORT')) : '' }}">
                                    <span class="input-group-addon pl-2 pr-2">to</span>
                                    <input type="text" name="to" id="to" class="to form-control datepicker" placeholder="To" value="{{ isset($to) ? \Helper::DateFormat($to, config('constant.DATE_FORMAT_SHORT')) : '' }}">
                                </div>        
                            </div>
                            <div class="mr-2 mb-2 flex-grow">
                                <label class="pr-1">Item:</label>
                                <select name="item" id="item" class="item form-control" onchange="fetchMarka(this)">
                                    <option value="">Select</option>
                                    @foreach($items as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mr-2 mb-2 flex-grow">
                                <label class="pr-1">Marka:</label>
                                <select name="marka" id="marka_id" class="marka form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <div class="mr-2 mb-2 flex-grow">
                                <label class="pr-1">Search</label>
                                <div class="input-group-icon input-group-icon-left">
                                    <span class="input-icon input-icon-right font-16"><i class="ti-search"></i></span>
                                    <input type="text" name="search" id="search" class="search form-control" placeholder="Vakkal Number" value="">
                                </div>
                            </div>  
                            <div class="mb-2 align-self-end">
                                <a class="btn btn-primary" href="javascript:void(0)" onclick="ajaxsearchfilter()">Filter</a>                                                                                  
                            </div>
                        </div>                                              
                    </div>    
                </div>  
                <div class="addlist"></div>          
                <br/><br/>
                <!-- <div class="row mb-5">
                    <div class="col-lg-6 col-md-8 col-sm-12 mb-2">   
                        <input type="text" name="vakkal_number" id="livesearchvakkalnumber" class="form-control" placeholder="Search Vakkal Number" value="">                          
                    </div>  
                    <div class="col-lg-6 col-md-4 col-sm-12">
                        <button type="button" class="btn btn-primary" onclick="getInwardData()">Add</button>
                    </div>
                </div>  -->

                <div class="alert alert-primary alert-bordered"><h5>Outward Item Entries</h5></div>            
                <div id="adddivision">                       
                @if(isset($order_items) && $order_items->count()>0)
                    @foreach($order_items as $order_item)
                        @php
                            if(isset($item_marka) && $item_marka->isNotEmpty()) {
                                $markas = $item_marka->get($order_item->item_id)->pluck('name','marka_id');                            
                            }
                            $markas->prepend("Select","");
                        @endphp
                    <div class="display-group">
                        <div class="row align-items-end">    
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
                                <label>Vakkal Number</label>
                                <input type="text" name="vakkal_number[]" class="form-control" placeholder="xxxx/xxx/xx" value="{{ $order_item->vakkal_number ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                                <label>Item</label>
                                <select name="item_id[]" id="item_id" class="form-control" required onchange="fetchMarka(this)">
                                    <option value="">Select</option>
                                    @foreach($items as $key => $value)
                                        <option value="{{ $key }}" @selected($order_item->item_id == $key)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2"> 
                                <label>Marka</label>
                                <select name="marka_id[]" id="marka_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($markas as $key => $value)
                                        <option value="{{ $key }}" @selected($order_item->marka_id == $key)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
                                <label>Details</label>  
                                <input type="text" name="details[]" id="details" class="form-control" placeholder="Details" maxlength="45" value="{{ $order_item->getRawOriginal('description') ?? '' }}">
                            </div>
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">    
                                <label>Bag Weight (in kg)</label>
                                <input type="text" name="weight[]" id="weight" class="form-control" placeholder="Bag Weight (in kg)" value="{{ $order_item->weight ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md-2 pl-sm-1 col-sm-4 pb-2"> 
                                <label>Quantity</label>     
                                <input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Quantity" value="{{ $order_item->quantity ?? '' }}" required>                     
                            </div>
                        </div>
                        <div class="row align-items-end">
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2">  
                                <label>Chamber</label>       
                                <select name="chamber_id[]" id="chamber_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($chambers as $key => $value)
                                        <option value="{{ $key }}" @selected($order_item->chamber_id == $key)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>    
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2"> 
                                <label>Floor</label>  
                                <select name="floor_id[]" id="floor_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($floors as $key => $value)
                                        <option value="{{ $key }}" @selected($order_item->floor_id == $key)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2"> 
                                <label>Grid</label>    
                                <select name="grid_id[]" id="grid_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($grids as $key => $value)
                                        <option value="{{ $key }}" @selected($order_item->grid_id == $key)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>    
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
                                <label>No. of Days</label>
                                <input type="text" name="no_of_days[]" id="no_of_days" class="form-control" placeholder="Number of Days" value="{{ $order_item->no_of_days ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                                <label>Cooling Charge Rate (per month/kg)</label>
                                <input type="text" name="rate[]" id="rate" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" value="{{ $order_item->rate ?? '' }}" required>
                            </div>
                            <div class="col-12 col-md pl-sm-1 col-sm-4 pb-2"> 
                                <label>Taxable</label> 
                                <select name="is_taxable[]" id="is_taxable" class="form-control" required>
                                    <option value="" @selected($order_item->is_taxable === null)>Select</option>
                                    <option value="1" @selected($order_item->is_taxable == '1')>Yes</option>
                                    <option value="0" @selected($order_item->is_taxable == '0')>No</option>
                                </select>
                            </div> 
                            <a class="remove-item text-danger custom-class" href="javascript:void(0)" onclick="removeItem(this);"><i class="fas fa-times-circle"></i></a>
                        </div>                                                   
                    </div>                              
                    @endforeach 
                @endif    
                </div>
                <!-- Hidden Template to add more -->
                <div class="display-group hide" id="ItemTemplate">
                    <div class="row align-items-end"> 
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
                            <label>Vakkal Number</label>
                            <input type="text" name="vakkal_number[]" id="vakkal_number" class="form-control" placeholder="xxxx/xxx/xx" required disabled>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Item</label>
                            <select name="item_id[]" id="item_id" class="form-control" required disabled onchange="fetchMarka(this)">
                                <option value="">Select</option>
                                @foreach($items as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2"> 
                            <label>Marka</label>
                            <select name="marka_id[]" id="marka_id" class="form-control" required disabled>
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2"> 
                            <label>Details</label>
                            <input type="text" name="details[]" id="details" class="form-control" placeholder="Details" maxlength="45" disabled>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Bag Weight (in kg)</label>
                            <input type="text" name="weight[]" id="weight" class="form-control" placeholder="Bag Weight (in kg)" required disabled>
                        </div>
                        <div class="col-12 col-md-2 pl-sm-1 col-sm-4 pb-2"> 
                            <label>Quantity</label>  
                            <input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Quantity" required disabled>    
                        </div>    
                    </div>
                    <div class="row align-items-end">
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
                            <label>Chamber</label>    
                            <select name="chamber_id[]" id="chamber_id" class="form-control" required disabled>
                                <option value="">Select</option>
                                @foreach($chambers as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>    
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">                           
                            <label>Floor</label>
                            <select name="floor_id[]" id="floor_id" class="form-control" required disabled>
                                <option value="">Select</option>
                                @foreach($floors as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>  
                        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2">                            
                            <label>Grid</label>
                            <select name="grid_id[]" id="grid_id" class="form-control" required disabled>
                                <option value="">Select</option>
                                @foreach($grids as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div> 
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
                            <label>No. of Days</label>
                            <input type="text" name="no_of_days[]" id="no_of_days" class="form-control" placeholder="Number of Days" disabled required>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Cooling Charge Rate (per month/kg)</label>
                            <input type="text" name="rate[]" id="rate" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" disabled required>
                        </div>
                        <div class="col-12 col-md pl-sm-1 col-sm-4 pb-2">  
                            <label>Taxable</label>    
                            <select name="is_taxable[]" id="is_taxable" class="form-control" required disabled>
                                <option value="">Select</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>
                        <a class="remove-item text-danger custom-class" href="javascript:void(0)" onclick="removeItem(this);"><i class="fas fa-times-circle"></i></a>
                    </div>    
                    <div class="mb-2 d-sm-none">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)"><i class="fa fa-minus"></i></button>
                    </div> 
                </div> 
                <!-- Template to add more -->
                <div class="row">
                    <div class="col-md-12">
                        <label class="m-t-5"><strong>Click (+Add) to add another entry</strong></label>                                    
                        <button type="button" class="btn btn-blue btn-sm" onclick="addItem()"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
                <br/><br/>  
                <div class="row">
                    <div class="form-group col">                            
                        <label for="notes">Notes (Max 85 characters)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Notes" maxlength="90" value="{{ ($customer_order->notes ?? '')}}"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-3 col-md-6 col-sm-12">                            
                        <label for="additional_charge">Additional Charge</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fas fa-rupee-sign"></i></span>
                            <input type="number" name="additional_charge" class="form-control" placeholder="Additional Charge" step="any" min="0" value="{{ ($customer_order->additional_charge ?? '')}}"/>
                        </div>                                                                                                                                      
                    </div>
                </div>    
                <div class="ibox-footer row">                    
                    <div class="col p-0">
                        <button type="submit" class="btn btn-info mr-2 mb-2" id="submitbtn">SAVE</button>
                        <a href="{{ route('admin.outwards.index')}}" class="btn btn-secondary mr-2 mb-2" data-dismiss="modal">CANCEL</a>
                    @if(isset($customer_order))    
                        <a href="{{ route('admin.outwards.showReceipt',$customer_order->customer_order_id) }}" class="btn btn-blue mb-2">VIEW RECEIPT</a>
                    @endif    
                    </div>
                </div>                                                          
            </div>                    
        </div>
    </form>
</div>    
@endsection

@section('plugin-scripts')
	<script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/0.11.1/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/0.11.1/typeahead.jquery.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/outwards/create.js') }}"></script>

    @if (session('type'))
        <script type="text/javascript">
    		@if(session('type')=="success")
    			$("#notify").notification({caption: "{{session('message')}}", sticky:false, type:'{{session('type')}}'});
    		@else
    			$("#notify").notification({caption: "{{session('message')}}", sticky:true, type:'{{session('type')}}'});
    		@endif
        </script>
    @endif
@endsection

