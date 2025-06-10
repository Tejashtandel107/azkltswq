@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/inwards/create.css') }}">    
@endsection

@section('pagecontent')
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">

@if(isset($customer_order))
    <form method="POST" action="{{ route('admin.inwards.update', $customer_order->customer_order_id) }}" id="customerorders-form" onsubmit="return OnFormSubmit(this, true)">
        @method('PATCH')
@else
    <form method="POST" action="{{ route('admin.inwards.store') }}" id="customerorders-form" onsubmit="return OnFormSubmit(this, false)">
@endif
    @csrf
        <div class="ibox ibox-fullheight">
            <input type="hidden" name="printinward" value="1" id="printinward">
            <div class="ibox-body">
                <div id="notify"></div>                
                <div class="alert alert-primary alert-bordered"><h5>Inward Info</h5></div>   
                <div class="row">
                    <div class="form-group col-md-4 col-12">                            
                        <label>Customer &nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openCustomerModalPopUp('{{route('admin.customers.openmodal')}}');" title="add new customer">Add <i class="la la-plus"></i></button></label>
                        <select name="customer_id" id="customer_id" class="form-control" required>
                            <option value="">Select</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->customer_id }}" @selected(isset($customer_order) && $customer_order->customer_id == $customer->customer_id)>
                                    {{ $customer->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('customer_id'))
                            <small class="error">{{ $errors->first('customer_id') }}</small>
                        @endif
                    </div>    
                    <div class="form-group col-md-4 col-sm-12">
                        <label for="date">Inward Date</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" name="date" class="form-control datepicker" placeholder="DD/MM/YYYY" value="{{ ($customer_order->date) ?? Helper::DateFormat(today(), config('constant.DATE_FORMAT_SHORT')) }}" required>
                        </div>    
                        @if ($errors->has('date'))
                            <small class="error">{{ $errors->first('date') }}</small>
                        @endif
                    </div>                                                
                    <div class="form-group col-md-4 col-sm-12">                            
                        <label for="sr_no">Serial Number</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="ti ti-receipt"></i></span>
                            <input type="number" name="sr_no" class="form-control" placeholder="Serial Number" value="{{ $customer_order->sr_no ?? null }}" required>
                        </div>                                                                                                                                      
                    </div>                                                                                                                              
                </div>                 
                <div class="row">
                    <div class="form-group col-md-4 col-sm-12">                            
                        <label for="from">From</label>
                        <input type="text" name="from" class="form-control" placeholder="From" value="{{  $customer_order->from ?? '' }}">
                    </div>                                                                                                                              
                    <div class="form-group col-md-4 col-sm-12">                            
                        <label for="transporter">Transporter</label>
                        <input type="text" name="transporter" class="form-control" placeholder="Transporter" value="{{  $customer_order->transporter ?? '' }}">
                    </div>                                                                                                                              
                    <div class="form-group col-md-4 col-sm-12">                            
                        <label for="vehicle">Vehicle Number</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fa fa-truck"></i></span>
                            <input type="text" name="vehicle" class="form-control" placeholder="Vehicle Number" value="{{ $customer_order->vehicle ?? '' }}">
                        </div>                                                                                                                                      
                    </div>                                                                                                                              
                </div>            
                <br/><br/>
                <div class="alert alert-primary alert-bordered"><h5>Inward Item Entries</h5></div>
            @if(isset($order_items) && $order_items->count()>0)
                @foreach($order_items as $order_item)
                    @php
                        if(isset($item_marka) && $item_marka->isNotEmpty()){
                            $markas = $item_marka->get($order_item->item_id)->pluck('name','marka_id');
                        }
                        $markas->prepend("Select","");
                    @endphp

                <div class="display-group">
                    <div class="row align-items-end">   
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
                            <label>Vakkal Number</label>
                            <input type="text" name="vakkal_number[]" id="vakkal_number" class="form-control" placeholder="xxxx/xxx/xx" value="{{ $order_item->vakkal_number }}" required>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Item&nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openItemModalPopUp('{{route('admin.items.openmodal')}}');" title="add new item">Add <i class="la la-plus"></i></button></label>
                            <select name="item_id[]" id="item_id" class="form-control" required onchange="fetchMarka(this)">
                                <option value="">Select</option>
                                @foreach($items as $key => $value)
                                    <option value="{{ $key }}" {{ ($order_item->item_id == $key) ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2"> 
                            <label>Marka&nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openMarkaModalPopUp('{{route('admin.item-marka.openmodal')}}');" title="add new marka">Add <i class="la la-plus"></i></button></label>                              
                            <select name="marka_id[]" id="marka_id" class="form-control" required>
                                @foreach($markas as $key => $value)
                                    <option value="{{ $key }}" {{ ($order_item->marka_id == $key) ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
                            <label>Details</label>       
                            <input type="text" name="details[]" id="details" class="form-control" placeholder="Details" maxlength="45" value="{{ $order_item->getRawOriginal('description') }}"> 
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">    
                            <label>Bag Weight (in kg)</label>
                            <input type="text" name="weight[]" id="weight" class="form-control" placeholder="Bag Weight (in kg)" required step="any" min="0" value="{{ $order_item->weight }}">                                 
                        </div>
                        <div class="col-12 col-md-2 pl-sm-1 col-sm-4 pb-2"> 
                            <label>Quantity</label> 
                            <input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Quantity" min="0" value="{{ $order_item->quantity }}" required>
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="row col-md-5 col-sm-12 pr-1 pb-2">
                            <div class="col pr-1">  
                                <label>Chamber</label>    
                                <select name="chamber_id[]" id="chamber_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($chambers as $key => $value)
                                        <option value="{{ $key }}" {{ ($order_item->chamber_id == $key) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>    
                            <div class="col pr-1 pl-1">      
                                <label>Floor</label>  
                                <select name="floor_id[]" id="floor_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($floors as $key => $value)
                                        <option value="{{ $key }}" {{ ($order_item->floor_id == $key) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col pr-0 pr-md-1 pl-1">
                                <label>Grid</label>    
                                <select name="grid_id[]" id="grid_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($grids as $key => $value)
                                        <option value="{{ $key }}" {{ ($order_item->grid_id == $key) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pb-2"> 
                            <label>Item Rate</label>     
                            <input type="text" name="item_rate[]" id="item_rate" class="form-control" placeholder="Item Rate" value="{{ $order_item->item_rate }}" required>     
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Insurance Rate(per month)</label>    
                            <input type="text" name="insurance_rate[]" id="insurance_rate" class="form-control" placeholder="Insurance Rate" value="{{ $order_item->insurance_rate }}" required>
                        </div>     
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Cooling Charge Rate (per month/kg)</label>       
                            <input type="text" name="rate[]" id="rate" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" value="{{ $order_item->rate }}" required>
                        </div> 
                        <div class="col-12 col-md col-sm-3 pl-sm-1 pb-2">
                            <label>Taxable</label> 
                            <select name="is_taxable[]" id="is_taxable" class="form-control" required>
                                <option value="">Select</option>
                                <option value="1" {{ $order_item->is_taxable == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $order_item->is_taxable == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>             
                        <a class="remove-item text-danger custom-class" href="javascript:void(0)" onclick="removeItem(this);"><i class="fas fa-times-circle"></i></a>
                    </div>    
                </div>                            
                @endforeach 
            @else                                
                <div class="display-group">
                    <div class="row align-items-end">  
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2">                        
                            <label>Vakkal Number</label>
                            <input type="text" name="vakkal_number[]" id="vakkal_number" class="form-control" placeholder="xxxx/xxx/xx" required>
                        </div>  
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Item &nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openItemModalPopUp('{{route('admin.items.openmodal')}}');" title="add new item">Add <i class="la la-plus"></i></button></label>
                            <select name="item_id[]" id="item_id" class="form-control" required onchange="fetchMarka(this)">
                                <option value="">Select</option>
                                @foreach($items as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2">
                            <label>Marka &nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openMarkaModalPopUp('{{route('admin.item-marka.openmodal')}}');" title="add new marka">Add <i class="la la-plus"></i></button></label>
                            <select name="marka_id[]" class="form-control" id="marka_id" required>
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
                            <label>Details</label>  
                            <input type="text" name="details[]" id="details" class="form-control" placeholder="Details" maxlength="45"> 
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Bag Weight (in kg)</label>
                            <input type="text" name="weight[]" id="weight" class="form-control" placeholder="Bag Weight (in kg)" required>                                 
                        </div>
                        <div class="col-12 col-md-2 pl-sm-1 col-sm-4 pb-2"> 
                            <label>Quantity</label>
                            <input type="text" name="quantity[]" id="quantity" class="form-control" placeholder="Quantity" required>
                        </div>   
                    </div>
                    <div class="row align-items-end">
                        <div class="row col-md-5 col-sm-12 pr-1 pb-2">
                            <div class="col pr-1">
                                <label>Chamber</label>
                                <select name="chamber_id[]" id="chamber_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($chambers as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>                   
                            </div>
                            <div class="col pr-1 pl-1">
                                <label>Floor</label>
                                <select name="floor_id[]" id="floor_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($floors as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>                                              
                            <div class="col pr-0 pr-md-1 pl-1">
                                <label>Grid</label>
                                <select name="grid_id[]" id="grid_id" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach($grids as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pb-2"> 
                            <label>Item Rate</label>    
                            <input type="text" name="item_rate[]" id="item_rate" class="form-control" placeholder="Item Rate" required>     
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Insurance Rate (per month)</label>    
                            <input type="text" name="insurance_rate[]" id="insurance_rate" class="form-control" placeholder="Insurance Rate" required>
                        </div>   
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Cooling Charge Rate (per month/kg)</label>    
                            <input type="text" name="rate[]" id="rate" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" required>
                        </div> 
                        <div class="col-12 col-md col-sm-3 pl-sm-1 pb-2">
                            <label>Taxable</label>                               
                        <select name="is_taxable[]" class="form-control" id="is_taxable" required>
                                <option value="">Select</option>
                                <option value="1">Yes</option>
                                <option value="0" selected>No</option>
                            </select>
                        </div>                        
                    </div>       
                </div>     
            @endif    
                <!-- Hidden Template to add more -->
                <div class="display-group hide" id="ItemTemplate">
                    <div class="row align-items-end"> 
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2">   
                            <label>Vakkal Number</label>
                            <input type="text" name="vakkal_number[]" class="form-control" placeholder="xxxx/xxx/xx" id="vakkal_number" required disabled>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
                            <label>Item &nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openItemModalPopUp('{{route('admin.items.openmodal')}}');" title="add new item">Add <i class="la la-plus"></i></button></label>
                            <select name="item_id[]" id="item_id" class="form-control" required disabled onchange="fetchMarka(this)">
                                <option value="">Select</option>
                                @foreach($items as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2">
                            <label>Marka &nbsp;&nbsp;<button type="button" class="btn btn-blue btn-sm py-0 px-1" onclick="openMarkaModalPopUp('{{route('admin.item-marka.openmodal')}}');" title="add new marka">Add <i class="la la-plus"></i></button></label>
                            <select name="marka_id[]" class="form-control" id="marka_id" required disabled>
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
                        <div class="row col-md-5 col-sm-12 pr-1 pb-2">
                            <div class="col pr-1">
                                <label>Chamber</label>                              
                                <select name="chamber_id[]" id="chamber_id" class="form-control" required disabled>
                                    <option value="">Select</option>
                                    @foreach($chambers as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>  
                            </div>    
                            <div class="col pr-1 pl-1">                               
                                <label>Floor</label>
                                <select name="floor_id[]" id="floor_id" class="form-control" required disabled>
                                    <option value="">Select</option>
                                    @foreach($floors as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>    
                            <div class="col pr-0 pr-md-1 pl-1">                              
                                <label>Grid</label>
                                <select name="grid_id[]" id="grid_id" class="form-control" required disabled>
                                    <option value="">Select</option>
                                    @foreach($grids as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pb-2"> 
                            <label>Item Rate</label>    
                            <input type="text" name="item_rate[]" id="item_rate" class="form-control" placeholder="Item Rate" required disabled>     
                        </div>
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Insurance Rate (per month)</label>   
                            <input type="text" name="insurance_rate[]" id="insurance_rate" class="form-control" placeholder="Insurance Rate" required disabled>
                        </div>   
                        <div class="col-12 col-md-2 col-sm-3 pr-sm-1 pl-sm-1 pb-2"> 
                            <label>Cooling Charge Rate (per month/kg)</label>    
                            <input type="text" name="rate[]" id="rate" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" required disabled>
                        </div> 
                        <div class="col-12 col-md col-sm-3 pl-sm-1 pb-2">
                            <label>Taxable</label>   
                            <select name="is_taxable[]" class="form-control" id="is_taxable" required disabled>
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
                    <div class="col-md-12" style="text-align: right">
                        <label class="m-t-5"><strong>Click (+Add) to add another entry</strong></label>                                    
                        <button type="button" class="btn btn-blue btn-sm" onclick="addItem()"><i class="fa fa-plus"></i> Add</button>
                    </div>
                </div>
                <br/><br/>                       
                <div class="row">
                    <div class="form-group col">                            
                        <label for="notes">Notes (Max 90 characters)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Notes" maxlength="90" value="{{ $customer_order->notes ?? '' }}">
                    </div>                
                </div>
                <div class="row">
                    <div class="form-group col-lg-3 col-md-6 col-sm-12">                            
                        <label for="additional_charge">Additional Charge</label>
                        <div class="input-group mb-2 date">
                            <span class="input-group-addon"><i class="fas fa-rupee-sign"></i></span>
                            <input type="number" name="additional_charge" class="form-control" placeholder="Additional Charge" step="any" min="0" value="{{ $customer_order->additional_charge ?? 0 }}" >
                        </div>                                                                                                                                      
                    </div>                
                </div>    
                <div class="ibox-footer row">                    
                    <div class="col p-0">
                        <button type="submit" class="btn btn-info mr-2 mb-2" id="submitbtn">SAVE</button>
                        <a href="{{ route('admin.inwards.index')}}" class="btn btn-secondary mr-2 mb-2" data-dismiss="modal">CANCEL</a>
                    @if(isset($customer_order))
                        <a href="{{ route('admin.inwards.showReceipt',$customer_order->customer_order_id) }}" class="btn btn-blue mb-2">VIEW RECEIPT</a>
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
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/inwards/create.js') }}"></script>
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
