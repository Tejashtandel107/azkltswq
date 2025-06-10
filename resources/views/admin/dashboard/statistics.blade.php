<div class="col-xl-12">
    <div class="ibox mb-0">
        
        <div class="ibox-body" style="border-bottom: 1px solid #eee;padding: 10px 20px;">
            <div class="d-flex flex-wrap">
                <div class="align-self-center flex-grow mb-2 heading">
                    <h5 class="font-strong">STATISTICS</h5>
                </div>
                <div class="align-self-center noprint mb-2">
                    <form class="form-inline" action="javascript:void(0);">
                        <label class="">From &nbsp;</label>                            
                        <div class="input-group date">
                            <input type="text" name="from_date" value="{{ $from ?? '' }}" class="form-control datepicker mr-sm-2 mb-0"  id="from_date" placeholder="From" autocomplete="off" />
                        </div>&nbsp;&nbsp;&nbsp;
                        <label class="">To &nbsp;</label>
                        <div class="input-group date">
                            <input type="text" name="to_date" value="{{ $to ?? '' }}" class="form-control datepicker mr-sm-2 mb-0"  id="to_date" placeholder="To" autocomplete="off" />
                        </div>    
                        <button class="btn btn-primary" type="submit" onclick="onChangeYear()">Filter</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="ibox-body">
            <div class="row mb-4">
                <div class="col-lg-3 col-6 text-center">
                    <div class="text-muted">TOTAL SALES</div>
                    <div class="h2 text-danger mt-1"><i class="fa fa-rupee"></i> {{ (isset($fishings_transaction) && isset($fishings_income) && $fishings_transaction->count() > 0 && $fishings_income > 0) ? Helper::formatAmount($fishings_income + $fishings_transaction->sum('fishing')) : '0.00' }}</div>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <div class="text-muted">TOTAL RECEIPTS</div>
                    <div class="h2 text-success mt-1"><i class="fa fa-rupee"></i> {{ (isset($incomes) && $incomes->count() > 0) ? Helper::formatAmount($incomes->sum('amount')) : '0.00'}}</div>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <div class="text-muted">TOTAL PAYMENTS</div>
                    <div class="h2 text-primary mt-1"><i class="fa fa-rupee"></i> {{ (isset($payments) && $payments->count() > 0) ? Helper::formatAmount($payments->sum('amount')) : '0.00'}}</div>
                </div>
                <div class="col-lg-3 col-6 text-center">
                    <div class="text-muted">TOTAL PROFIT</div>
                    <div class="h2 text-pink mt-1"><i class="fa fa-rupee"></i> {{ (isset($payments) && isset($incomes) && $payments->count() > 0 && $incomes->count() > 0) ? Helper::formatAmount($incomes->sum('amount') - $payments->sum('amount')) : '0.00'}}</div>
                </div>                        
            </div>                    
        </div>
    </div>
</div>