@php
    $auth_user_id = Auth::id();
    $can_view_receipt = ($auth_user_id == $user->user_id and $user->customer_id == $customer_order->customer_id) ? true : false;
@endphp
@extends('web.layouts.app')

@section('pagetitle',$pagetitle)

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/web/css/outwards/show-receipt.css') }}">
@endsection

@section('pagecontent')
@include('web.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox" id="printbox">
        <div class="ibox-body text-black">
        @if($can_view_receipt)
            <div class="d-flex justify-content-end">
                <div class="noprint mb-3">
                    <a class="btn btn-dark" href="javascript:void(0)" onclick="printReport();"><i class="fa fa-print"></i> Print</a>
                </div> 
            </div>

            @if(isset($outward_items) && $outward_items->count() > 0)
                @php
                    $i = 1;
                    $total_page = $outward_items->chunk(5)->count();
                @endphp
                @foreach($outward_items->chunk(5) as $items)
                <!-- <div class="pagebreak">
                </div> -->
                <div class="border border-black {{($loop->first) ? '' : 'print-margin-top pagebreak'}}">
                    {{--<div class="text-center top-title border-bottom p-1">|| {{$company_info->gods_quotes}} ||</div>--}}
                    <div class="d-flex align-items-center justify-content-between p-1 border-bottom">
                        <div class="col"></div>
                        <div class="col text-center top-title">|| {{$company_info->gods_quotes}} ||</div>
                        <div class="col text-right">Page {{$i++}} of {{$total_page}}</div>
                    </div>
                    <div class="print-padding header-bg d-flex align-items-center justify-content-between p-1 border-bottom">
                        <div class="col">GSTIN: {{$company_info->gstnumber}}</div>
                        <div class="col text-center font-weight-bold font-20 print-title">OUTWARD CHALLAN</div>
                        <div class="col text-right">Contact: {{$company_info->phone}}</div>
                    </div>
                    <div class="print-padding text-center p-2 border-bottom">
                        <div class="pb-1 font-weight-bold font-20 print-p-b-0">
                            {{strtoupper($company_info->companyname)}}
                        </div>
                        <div>{{ $company_info->address }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="w-100 print-font-size">
                            <thead>
                                <tr class="border-bottom">
                                    <td colspan="7" class="p-1 border-right print-padding">
                                        <div class="pb-2">Party name : &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->fullname}}</span></div>
                                        <div class="pb-3">Address:&nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->customer_add}}</span></div>
                                        <div>Delivery To: &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->delivery_address}}</span></div>
                                    </td>
                                    <td class="p-1 print-padding" colspan="4">
                                        <div class="pb-2 print-p-b-0">Date : &nbsp;&nbsp;<span class="font-weight-bold">{{ $customer_order->date }}</span></div>
                                        <div class="pb-2 print-p-b-0">Sr. No.: &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->sr_no}}</span></div>
                                        <div class="pb-2 print-p-b-0">Order: &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->order_by}}</span></div>
                                        <div>Vehicle No : &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->vehicle}}</span></div>
                                    </td>
                                </tr>
                                <tr class="text-center border-bottom font-weight-bold item-font-size">
                                    <td class="border-right title-vakkal">VAKKAL NO.</td>
                                    <td class="border-right title-item-marka">Item-Marka</td>
                                    <td class="border-right title-details">Details</td>
                                    <td class="w-8 border-right border-bottom">Location Code</td>
                                    <td class="w-8 border-right title-weight">Weight (KG)</td>
                                    <td class="w-8 border-right title-qty">Quantity</td>
                                    <td class="border-right border-bottom title-balance-qty">Balanced Quantity</td>
                                    <td class="w-8 border-right border-bottom">Total Weight</td>
                                    <td class="w-8 border-right border-bottom">No of days</td>
                                    <td class="w-8 border-right border-bottom">Rate per kg</td>
                                    <td class="border-bottom title-amount">Amount <br>Rs.</td>
                                </tr>
                            </thead>
                            <tbody>
                    <?php
                        $total_weight = 0;
                        $total_quantity = 0;
                        $total_amount = 0;
                        $additional_change = $customer_order->additional_charge;
                    ?>
                    @if($items && $items->count() > 0)
                        @foreach ($items as $outward_item)
                            @php
                                $total_weight += $outward_item->total_weight;
                                $total_quantity += $outward_item->quantity;
                                $total_amount += $outward_item->total_amount;
                            @endphp
                            <tr class="text-center item-font-size">
                                <td class="border-right text-center">{{$outward_item->vakkal_number}}</td>
                                <td class="border-right text-center">{{$outward_item->item_name}}-{{$outward_item->marka_name}}</td>
                                <td class="border-right text-center">{{$outward_item->description}}</td>
                                <td class="border-right text-center">{{ Helper::getLocationCode(($outward_item->chamber_number) ?? ' -- ',($outward_item->floor_number) ?? ' -- ',($outward_item->grid_number) ?? ' --') }}</td>
                                <td class="border-right text-center">{{$outward_item->weight}}</td>
                                <td class="border-right text-center">{{$outward_item->quantity}}</td>
                                <td class="border-right text-center">{{$outward_item->balance_quantity}}</td>
                                <td class="border-right text-center">{{ Helper::formatWeight($outward_item->total_weight) }}</td>
                                <td class="border-right text-center">{{ $outward_item->no_of_days ?? 0.00}}</td>
                                <td class="border-right text-center">{{ $outward_item->rate ?? 0.00}}</td>
                                <td class="text-center">{{Helper::formatAmount($outward_item->total_amount)}}</td>
                            </tr>
                        @endforeach
                        @if($items->count() < 5)
                            @for($i=1;$i<=5-$items->count(); $i++)
                                <tr class="text-center item-font-size">
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td class="border-right">0.00</td>
                                    <td class="border-right"></td>
                                    <td class="border-right"></td>
                                    <td>0.00</td>
                                </tr>
                            @endfor
                        @endif
                    @endif
                                <tr class="border-top text-center item-font-size">
                                    <td class="border-right" colspan="4"></td>
                                    <td class="border-right border-bottom">Total</td>
                                    <td class="border-right border-bottom">{{$total_quantity}}</td>
                                    <td class="border-right border-bottom"></td>
                                    <td class="border-right border-bottom">{{Helper::formatWeight($total_weight)}}</td>
                                    <td class="border-right border-bottom"></td>
                                    <td class="border-right border-bottom"></td>
                                    <td class="border-bottom text-right">{{Helper::formatAmount($total_amount)}}</td>
                                </tr>                                
                                <tr class="item-font-size">
                                    <td class="border-right pl-1" colspan="7">Remark: <span class="notes">{{ substr($customer_order->notes,0,85) }}</span></td>
                                    <td class="border-right border-bottom text-right" colspan="3"><b>Additional Charge:</b></td>
                                    <td class="border-bottom text-right"><b>{{($loop->first) ? Helper::formatAmount($additional_change) : Helper::formatAmount(0)}}</b></td>
                                </tr>
                                <tr class="item-font-size">
                                    <td class="border-right pl-1 print-remark" colspan="7">* all the items condition and location verify by sender sign</td>
                                    <td class="border-right border-bottom text-right" colspan="3"><b>Grand Total:</b></td>
                                    <td class="border-bottom text-right"><b>{{($loop->first) ? Helper::formatAmount($total_amount + $additional_change) : Helper::formatAmount($total_amount)}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>    
                    <div class="border-bottom">
                        <div class="pl-1 print-remark">* our risk and responsibility ceases as soon as the goods leave our permises</div>
                    </div>
                    <div class="p-3 print-padding">
                        <div class="text-right">
                            <b>For, {{ucwords(strtolower($company_info->companyname))}}</b>
                        </div>
                        <div class="pt-4 d-flex justify-content-between">
                            <div class="flex-grow font-weight-bold">Transporter/Receiver Sign.</div>
                            <div class="flex-grow text-right">Authorised Signature</div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif                                               
        @else
            <div class="text-left mt-3 alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon noprint"></i> Access is denied. </div>
        @endif
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/web/js/outwards/show-receipt.js') }}"></script>
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

