<div class="ibox ibox-fullheight">
    <div class="ibox-head">
        <div class="ibox-title">Outstanding Payments</div>
    </div>
    <div class="ibox-body pt-0 slimScrollDiv">
        <ul class="media-list scroller">
      @if(isset($outstanding_payments) && $outstanding_payments->count() > 0)
        @foreach($outstanding_payments as $outstanding_payment)
        <?php
            $insurances = $outstanding_payment->getInsurancePayment($request->merge(['from'=>Helper::DateFormat($outstanding_payment->last_invoice_date,config('constant.DATE_FORMAT_SHORT')),'c'=>$outstanding_payment->customer_id]));
            $total_amount = 0;
            foreach($insurances as $result){
              $balance_quantity = $result->inwards - $result->outwards;
              $total_weight = $balance_quantity * $result->weight;
              $valuation = $total_weight * $result->item_rate;
              $amount = ($valuation/100000)*$result->insurance_rate;
              $total_amount += $amount;
            }
        ?>
            <li class="border-bottom-1 py-2">
                <div class="flex-grow-1 d-flex justify-content-between flex-wrap">
                    <div class="">{{$outstanding_payment->companyname}}</div>
                    <div class="font-13 text-light d-flex flex-wrap">
                      <div class="mr-3">Last Invoice Date: {{Helper::DateFormat($outstanding_payment->last_invoice_date,config('constant.DATE_FORMAT_SHORT'))}}</div>
                      <div>Invoice Limit: {{Helper::formatAmount($outstanding_payment->invoice_limit)}}&nbsp;Rs</div>
                    </div>
                </div>
                <div class="flex-grow-1 d-flex flex-warp justify-content-between">
                  <div class="text-center p-2">
                      <div class="h6 mb-0 font-strong2">{{Helper::formatAmount($outstanding_payment->total_storage_charge)}}&nbsp;<span class="h6">Rs</span></div>
                      <small class="font-11">Storage Charge</small>
                  </div>
                  <div class="text-center p-2">
                    <div class="h6 mb-0 font-strong">{{Helper::formatAmount($total_amount)}}&nbsp;<span class="h6">Rs</span></div>
                      <small class="font-11">Insurance Charge</small>
                  </div>
                  <div class="text-center p-2">
                    <div class="h6 mb-0 font-strong">{{Helper::formatAmount($outstanding_payment->total_inward_additional_charge)}}&nbsp;<span class="h6">Rs</span></div>
                      <small class="font-11">Inward Charge</small>
                  </div>
                  <div class="text-center p-2">
                    <div class="h6 mb-0 font-strong">{{Helper::formatAmount($outstanding_payment->total_outward_additional_charge)}}&nbsp;<span class="h6">Rs</span></div>
                      <small class="font-11">Outward Charge</small>
                  </div>
                </div>
          </li>
          @endforeach
        @else
          <li class="border-bottom-1 py-2">
            <div class="text-left mt-3 alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon noprint"></i> Sorry. No outstanding payment found. </div>
          </li>
        @endif
        </ul>
    </div>
</div>