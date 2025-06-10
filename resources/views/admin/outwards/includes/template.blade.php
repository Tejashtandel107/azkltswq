<!-- Template to load json data -->
@php
    use Carbon\Carbon;

    $order_date = ($outward_order_date) ?  Carbon::createFromFormat(config('constant.DATE_FORMAT_SHORT'),$outward_order_date)->startOfDay() : Carbon::now();

    $adjust_date = $order_date->copy()->firstOfMonth();
    $adjust_date = $adjust_date->addDays(14);
    
    if($adjust_date->greaterThanOrEqualTo($order_date)){
        $final_date = $adjust_date;
    }
    else{
        $final_date = $order_date->copy()->lastOfMonth();
        //$final_date = new Carbon('last day of this month');
    }
@endphp
@foreach($order_items as $order_item)
    @php
        if(isset($item_marka) && $item_marka->isNotEmpty()) {
            $markas = $item_marka->get($order_item->item_id)->pluck('name','marka_id');                            
        }
        $markas->prepend("Select","");
    
        $date = Carbon::createFromFormat(config('constant.DATE_FORMAT_SHORT'),$order_item->date)->startOfDay();
        
        $days_diff = $date->diffInDays($final_date->addDays(1),false);
    @endphp
<div class="display-group">
    <div class="row align-items-end"> 
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
            <label>Vakkal Number</label>
            <input type="text" name="vakkal_number[]" value="{{ ($order_item->vakkal_number ?? '') }}" class="form-control" placeholder="xxxx/xxx/xx" required />
        </div>
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
            <label>Item</label>
            <select name="item_id[]" id="item_id" class="form-control" onchange="fetchMarka(this)" required>
                <option value="">Select</option>
                @foreach($items as $key => $value)
                    <option value="{{ $key }}" {{ (isset($order_item->item_id) && $order_item->item_id == $key) ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2"> 
            <label>Marka</label>
            <select name="marka_id[]" id="marka_id" class="form-control" required>
                @foreach($markas as $key => $value)
                    <option value="{{ $key }}" {{ (isset($order_item->marka_id) && $order_item->marka_id == $key) ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
            <label>Details</label>
            <input type="text" name="details[]" class="form-control" placeholder="Details" maxlength="45" value="{{ $order_item->getRawOriginal('description') }}">
        </div>
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
            <label>Bag Weight (in kg)</label>
            <input type="text" name="weight[]" class="form-control" placeholder="Bag Weight (in kg)" value="{{ $order_item->weight }}" required>
        </div>
        <div class="col-12 col-md-2 pl-sm-1 col-sm-4 pb-2"> 
            <label>Quantity</label>  
            <input type="text" name="quantity[]" class="form-control" placeholder="Quantity" value="{{ $order_item->quantity }}" required>
        </div>
    </div>
    <div class="row align-items-end">
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pb-2"> 
            <label>Chamber</label>  
            <select name="chamber_id[]" class="form-control" required>
                <option value="">Select</option>
                @foreach($chambers as $key => $value)
                    <option value="{{ $key }}" @selected($key == $order_item->chamber_id)>{{ $value }}</option>
                @endforeach
            </select>
        </div>    
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">                          
            <label>Floor</label>
            <select name="floor_id[]" class="form-control" required>
                <option value="">Select</option>
                @foreach($floors as $key => $value)
                    <option value="{{ $key }}" @selected($key == $order_item->floor_id)>{{ $value }}</option>
                @endforeach
            </select>
        </div>  
        <div class="col-12 col-md-2 col-sm-4 pr-md-1 pl-sm-1 pb-2">                             
            <label>Grid</label>
            <select name="grid_id[]" class="form-control" required>
                <option value="">Select</option>
                @foreach($grids as $key => $value)
                    <option value="{{ $key }}" @selected($key == $order_item->grid_id)>{{ $value }}</option>
                @endforeach
            </select>
        </div>   
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-md-1 pb-2">
            <label>No. of Days</label>
            <input type="text" name="no_of_days[]" class="form-control" placeholder="Number of Days" value="{{ ($days_diff ?? '')}}" required>
        </div>
        <div class="col-12 col-md-2 col-sm-4 pr-sm-1 pl-sm-1 pb-2">
            <label>Cooling Charge Rate (per month/kg)</label>
            <input type="text" name="rate[]" class="form-control" placeholder="Cooling Charge Rate (per month/kg)" value="{{ ($order_item->rate ?? '') }}" required>
        </div>
        <div class="col-12 col-md pl-sm-1 col-sm-4 pb-2">  
            <label>Taxable</label> 
            <select name="is_taxable[]" id="is_taxable" class="form-control" required>
                <option value="" @selected($order_item->is_taxable === null || $order_item->is_taxable === '')>Select</option>
                <option value="1" @selected($order_item->is_taxable == '1')>Yes</option>
                <option value="0" @selected($order_item->is_taxable == '0')>No</option>
            </select>
        </div>  
        <a class="remove-item text-danger custom-class" href="javascript:void(0)" onclick="removeItem(this);"><i class="fas fa-times-circle"></i></a>
    </div>    
    <div class="mb-2 d-sm-none">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)"><i class="fa fa-minus"></i></button>
    </div> 
</div> 
@endforeach