<?php 
    $from_date =  ($request->filled('from')) ? Helper::convertDateFormat($request->input('from'))->format('Y-m') : "";
    $to_date =  ($request->filled('to')) ? Helper::convertDateFormat($request->input('to'))->format('Y-m') : "";

    $total_valuation = $total_amount =  0;
    if(isset($insurance_statistics) && $insurance_statistics->count() > 0){
        foreach ($insurance_statistics as $result) {
            $balance_quantity = $result->inwards - $result->outwards;
            $total_weight = $balance_quantity * $result->weight;
            $valuation = $total_weight * $result->item_rate;
            $amount = ($valuation/100000)*$result->insurance_rate;
            $total_valuation += $valuation;
            $total_amount += $amount;
        }
    }     
?>
<div class="alert alert-success alert-bordered">Stocks Statistics</div>
<div class="row">
    <div class="col-lg-4 col-md-6">
        <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-success">
                    <span class="easypie-data" style="font-size:30px;color:#fff">
                        <i class="fas fa-truck-loading"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-success">{{Helper::formatWeight($calculate_stocks->total_inward_weight)}}</span>&nbsp;<span class="h5 text-success">KG</span>
                    <div class="text-uppercase">TOTAL INWARDS STOCKS</div>
                    <a class="badge badge-success" target="_blank" href="{{route('user.reports.full-ledger.show',['from'=>($request->from) ?? null ,'to'=>($request->to) ?? null,'c'=>($request->c) ?? null])}}" data-toggle="tooltip" data-placement="top" title="Click to view stocks detail report" data-original-title="Click to view stocks detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
         <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-success">
                    <span class="easypie-data" style="font-size:28px;color:#fff">
                        <i class="fas fa-truck"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-success">{{Helper::formatWeight($calculate_stocks->total_outward_weight)}}</span>&nbsp;<span class="h5 text-success">KG</span>
                    <div class="text-uppercase">TOTAL OUTWARDS STOCKS</div>
                    <a class="badge badge-success" target="_blank" href="{{route('user.reports.full-ledger.show',['from'=>($request->from) ?? null ,'to'=>($request->to) ?? null,'c'=>($request->c) ?? null])}}" data-toggle="tooltip" data-placement="top" title="Click to view stocks detail report" data-original-title="Click to view stocks detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
         <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-success">
                    <span class="easypie-data" style="font-size:45px;color:#fff">
                        T</span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-success">{{Helper::formatWeight($stock_statistics->sum('total_balance_weight'))}}</span>&nbsp;<span class="h5 text-success">KG</span>
                    <div class="text-uppercase">TOTAL CURRENT BALANCE STOCKS</div>
                    <a class="badge badge-success" target="_blank" href="{{route('user.reports.full-ledger.show',$request->all())}}" data-toggle="tooltip" data-placement="top" title="Click to view stocks detail report" data-original-title="Click to view stocks detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>  
<div class="alert alert-pink alert-bordered">Insurance Statistics</div>
<div class="row">
     <div class="col-lg-4 col-md-6">
        <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-pink">
                    <span class="easypie-data" style="font-size:30px;color:#fff">
                        <i class="fas fa-weight"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-pink">{{Helper::formatWeight($insurance_statistics->sum('total_balance_weight'))}}</span>&nbsp;<span class="h5 text-pink">KG</span>
                    <div class="text-uppercase">Total Stocks Weight</div>
                    <a class="badge badge-pink" target="_blank" href="{{route('user.reports.insurance-report.show',['from'=>$from_date,'to'=>$to_date,'c'=>($request->c) ?? null])}}" class="text-pink" data-toggle="tooltip" data-placement="top" title="Click to view insurance detail report" data-original-title="Click to view insurance detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-pink">
                    <span class="easypie-data" style="font-size:45px;color:#fff">
                        V</span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-pink">{{Helper::formatAmount($total_valuation)}}</span>&nbsp;<span class="h5 text-pink">Rs</span>
                    <div class="text-uppercase">Total Valuation </div>
                    <a class="badge badge-pink" target="_blank" href="{{route('user.reports.insurance-report.show',['from'=>$from_date,'to'=>$to_date,'c'=>($request->c) ?? null])}}" class="text-pink" data-toggle="tooltip" data-placement="top" title="Click to view insurance detail report" data-original-title="Click to view insurance detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
         <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-pink">
                    <span class="easypie-data" style="font-size:28px;color:#fff">
                        <i class="fas fa-rupee-sign"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-pink">{{Helper::formatWeight($total_amount)}}</span>&nbsp;<span class="h5 text-pink">Rs</span>
                    <div class="text-uppercase">Total Amount </div>
                    <a class="badge badge-pink" target="_blank" href="{{route('user.reports.insurance-report.show',['from'=>$from_date,'to'=>$to_date,'c'=>($request->c) ?? null])}}" class="text-pink" data-toggle="tooltip" data-placement="top" title="Click to view insurance detail report" data-original-title="Click to view insurance detail report">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
</div> 
<div class="alert alert-primary alert-bordered">Inwards / Outwards</div>
<div class="row">
    <div class="col-lg-4 col-md-6">
         <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-primary">
                    <span class="easypie-data" style="font-size:30px;color:#fff">
                        <i class="fas fa-truck-loading"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-primary">{{Helper::formatAmount($additional_charge->total_inward_additional_charge)}}</span>&nbsp;<span class="h5 text-primary">Rs</span>
                    <div class="text-uppercase">INWARDS ADDITIONAL CHARGES</div>
                    <a class="badge badge-primary" href="{{route('user.inwards.index',['from'=>($request->from) ?? null ,'to'=>($request->to) ?? null,'customer_id' => ($request->c) ?? null])}}" target="_blank" class="text-purple" data-toggle="tooltip" data-placement="top" title="Click to view inwards detail" data-original-title="Click to view inwards detail">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-primary">
                    <span class="easypie-data" style="font-size:30px;color:#fff">
                        <i class="fas fa-truck"></i></span>
                        <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-primary">{{Helper::formatAmount($additional_charge->total_outward_additional_charge)}}</span>&nbsp;<span class="h5 text-primary">Rs</span>
                    <div class="text-uppercase">OUTWARDS ADDITIONAL CHARGES</div>
                    <a class="badge badge-primary" href="{{route('user.outwards.index',['from'=>($request->from) ?? null ,'to'=>($request->to) ?? null,'customer_id' => ($request->c) ?? null])}}" target="_blank" class="text-purple" data-toggle="tooltip" data-placement="top" title="Click to view outwards detail" data-original-title="Click to view outwards detail">
                        View Details&nbsp;<i class="fas fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
         <div class="card mb-4">
            <div class="card-body flexbox-b p-0">
                <div class="easypie mr-4 p-2 bg-primary">
                    <span class="easypie-data" style="font-size:30px;color:#fff"><i class="fas fa-rupee-sign"></i></span>
                    <canvas height="80" width="80"></canvas>
                    </div>
                <div>
                    <span class="h3 font-strong text-primary">{{Helper::formatAmount($outward_total_amount)}}</span>&nbsp;<span class="h5 text-primary">Rs</span>
                    <div class="text-uppercase">TOTAL STORAGE CHARGES</div>
                </div>
            </div>
        </div>
    </div>
</div> 