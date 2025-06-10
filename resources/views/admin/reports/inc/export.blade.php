<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <title>GKFP - Export Stocks</title>    
</head>
<body>    
<div class="ibox">
    <div class="ibox-body">                
@if(isset($results) && ($results->count() > 0))
<?php    
        $full_ledgers = $results->groupBy('customer_id');
?>        
        @foreach($full_ledgers as $ledger_results)
<?php    
        $customer_info = $ledger_results->first();        
?>
        <div class="table-responsive">
            <table class="w-100">
                <thead>
                    <tr>
                        <td>Customer:</td>
                        <td>{{ $customer_info->fullname }}</td>
                        <td colspan="1"></td>
                        <td>Stock data till:</td>
                        <td colspan="4">
                            {{ Helper::DateFormat($ledger_results->min('order_date'),'d/m/Y') }} to {{ Helper::DateFormat($ledger_results->max('order_date'),'d/m/Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td>Type</td>
                        <td>Sr.No.</td>
                        <td>Item</td>
                        <td>Marka</td>
                        <td>Vakkal No.</td>
                        <td>Location Code</td>
                        <td>Weight</td>
                        <td>Quantity</td>
                        <td>Outstanding Quantity</td>
                        <td>Outstanding Weight</td>
                    </tr>
                </thead>
                <tbody>                    
<?php
                $chambers = App\Models\Chamber::all()->keyBy('chamber_id');
                $floors = App\Models\Floor::all()->keyBy('floor_id');
                $grids = App\Models\Grid::all()->keyBy('grid_id');
?>
                @foreach($ledger_results as $result)
<?php            
                    $chamber = $chambers->get($result->chamber_id);
                    $floor = $floors->get($result->floor_id);
                    $grid = $grids->get($result->grid_id);
?>
                    <tr>
                        <td>{{ Helper::DateFormat($result->order_date,config('constant.DATE_FORMAT_SHORT')) }}</td>
                        <td>{{ $result->type }}</td>
                        <td>{{ $result->sr_no }}</td>
                        <td>{{ $result->item_name }}</td>                            
                        <td>{{ $result->marka_name }}</td>                            
                        <td>{{ $result->vakkal_number }}</td>
                        <td>{{ Helper::getLocationCode( (($chamber) ? $chamber->number : ' -- '),(($floor) ? $floor->number : ' -- '),(($grid) ? $grid->number : ' -- ') ) }}</td>                        
                        <td>{{ $result->weight }}</td>
                        <td>{{ $result->quantity }}</td>
                        <td>{{ $result->total_inward-$result->total_outward }}</td>                                                        
                        <td>{{ $result->total_balance_weight }}</td>
                    </tr>
                @endforeach
            @endforeach
        @else
        <tr>
            <td colspan="12">
                <div> No records found. </div>
            </td>
        </tr>        
        @endif
                </tbody>
            </table>
        </div> 
    </div>
</div>
</body>
</html>
