@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/inwards/show-receipt.css') }}">
@endsection

@section('pagecontent')
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox" id="printbox">
        <div class="ibox-body text-black">
            <div class="d-flex justify-content-between">
                <div class="noprint mb-3">
                    <a class="btn btn-blue" href="{{ route('admin.inwards.edit',$customer_order->customer_order_id) }}" title="Edit"><i class="fa fa-edit"></i> Back To Edit</a>
                </div>
                <div class="noprint mb-3">
                    <a class="btn btn-dark" href="javascript:void(0)" onclick="printReport();"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>     
            @if(isset($order_items) && $order_items->count() > 0)
                @php
                    $i = 0;
                    $page = 1;
                    $total_page = $order_items->chunk(5)->count();
                @endphp
            @foreach($order_items->chunk(5) as $items)
            <div class="border border-black main-wrapper {{($loop->first) ? '' : 'print-margin-top pagebreak'}}">
                {{--<div class="text-center top-title border-bottom">|| {{$company_info->gods_quotes}} ||</div>--}}
                <div class="d-flex align-items-center justify-content-between p-1 border-bottom">
                    <div class="col"></div>
                    <div class="col text-center top-title">|| {{$company_info->gods_quotes}} ||</div>
                    <div class="col text-right">Page {{$page++}} of {{$total_page}}</div>
                </div>
                <div class="d-flex border-bottom print-padding align-items-center">
                    <div class="col">GSTIN: {{$company_info->gstnumber}}</div>
                    <div class="col text-center font-20 font-weight-bold print-title">INWARD CHALLAN</div>
                    <div class="col text-right">Contact: {{$company_info->phone}}</div>
                </div>
                <div class="text-center p-2 print-padding border-bottom">
                    <div class="pb-1 font-weight-bold font-20">
                        {{strtoupper($company_info->companyname)}}
                    </div>
                    <div>
                        {{$company_info->address}}
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="w-100">
                        <thead>
                            <tr class="border-bottom">
                                <td colspan="7" class="border-right p-1 print-padding">
                                    <div class="pb-1">
                                        Party name : &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->fullname}}</span>
                                    </div>
                                    <div class="pb-3">
                                        Address:&nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->customer_add}}</span>
                                    </div>
                                    <div>From : &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->from}}</span></div>
                                </td>
                                <td class="p-1 print-padding" colspan="3">
                                    <div class="pb-1 print-p-b-0">Date : &nbsp;&nbsp;<span class="font-weight-bold">
                                        {{ $customer_order->date }}</span>                                    
                                    </div>
                                    <div class="pb-1 print-p-b-0">
                                        Sr. No.: &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->sr_no}}</span>
                                    </div>
                                    <div class="pb-1 print-p-b-0">
                                        Transporter: &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->transporter}}</span>
                                    </div>
                                    <div>
                                        Vehicle No : &nbsp;&nbsp;<span class="font-weight-bold">{{$customer_order->vehicle}}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="font-weight-bold text-center border-bottom">
                                <td style="width: 41px" class="border-right text-center" rowspan="2">No#</td>
                                <td class="border-right text-center" rowspan="2">Item-Marka</td>
                                <td class="border-right text-center w-100px" rowspan="2">Details</td>
                                <td class="w-15 border-right text-center" rowspan="2">Vakkal NO.</td>
                                <td class="w-20 border-right border-bottom text-center" colspan="3">Location Code</td>
                                <td class="w-10 border-right text-center" rowspan="2">Weight (KG)</td>
                                <td class="w-10 border-right text-center" rowspan="2">Quantity</td>
                                <td class="text-center" rowspan="2">Total <br> weight</td>
                            </tr>
                            <tr class="font-weight-bold text-center border-bottom">
                                <td class="text-center border-right">CH.</td>
                                <td class="text-center border-right">Floor</td>
                                <td class="text-center border-right">Grid</td>
                            </tr>
                        </thead>
                        <tbody>
                    @php
                        $additional_change = $customer_order->additional_charge;
                    @endphp
                    @if(isset($order_items) && $order_items->count() > 0)
                        @foreach($items as $order_item)
                            @php
                                $i++;
                            @endphp
                            <tr class="text-center">
                                <td class="border-right">{{$i}}</td>
                                <td class="border-right">{{$order_item->item_name}}-{{$order_item->marka_name}}</td>
                                <td class="border-right">{{$order_item->description}}</td>
                                <td class="border-right">{{$order_item->vakkal_number}}</td>
                                <td class="border-right">{{$order_item->chamber_number}}</td>
                                <td class="border-right">{{$order_item->floor_number}}</td>
                                <td class="border-right">{{ Helper::getGrigNumber($order_item->grid_number) }}</td>
                                <td class="border-right">{{$order_item->weight}}</td>
                                <td class="border-right">{{$order_item->quantity}}</td>
                                <td>{{ $order_item->total_weight}}</td>
                            </tr>
                        @endforeach
                        @if($items->count() < 5)
                            @for($i=1;$i<=5-$items->count(); $i++)
                            <tr class="text-center">
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td class="border-right"></td>
                                <td>0.00</td>
                            </tr>
                            @endfor
                        @endif
                    @endif
                    @php
                        $total_weight = $items->sum('total_weight');
                    @endphp
                            <tr class="border-top print-remark">
                                <td class="border-right" colspan="7">Remark: <span class="notes">{{ substr($customer_order->notes,0,90) }}</span></td>                            
                                <td class="border-bottom border-right text-center"><b>TOTAL</b></td>
                                <td class="border-bottom border-right text-center"><b>{{Helper::formatAmount($items->sum('quantity')) }}</b></td>
                                <td class="border-bottom text-right"><b>{{Helper::formatAmount($total_weight)}}</b></td>                            
                            </tr>
                            <tr class="print-remark">   
                                <td class="border-right pl-2" colspan="7">* We only consider average weight of package by checking few of them</td>
                                <td class="border-right border-bottom text-right" colspan="2"><b>Additional Charge:</b></td>                            
                                <td class="border-bottom text-right"><b>{{($loop->first) ? Helper::formatAmount($additional_change) : Helper::formatAmount(0)}}</b></td>
                            </tr>
                            <tr class="print-remark">
                                <td class="pl-2" colspan="6">* all the items condition and location verify by sender sign</td>
                                <td class="pr-2 text-right" colspan="3"><b>By :</b> {{ $customer_order->username }} On {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $customer_order->order_created_date)->format('d/m/Y H:i')}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>    
                <div class="pl-2 border-bottom print-remark">
                    <div>* ones goods arrives, sender would be bound by terms & conditions</div>
                    <div class="font-weight-bold pt-1">*Please, read the terms and condition at back side before sign.</div>
                </div>
                <div class="p-3 print-padding print-padding">
                    <div class="text-right pr-3">
                        <b>For, {{ucwords(strtolower($company_info->companyname))}}</b>
                    </div>
                    <div class="pt-3 d-flex justify-content-between">
                        <div class="flex-grow pl-5">Transporter Sign.</div>
                        <div class="flex-grow text-center">Party Sign.</div>
                        <div class="flex-grow text-right pr-3">Authorised Signature</div>
                    </div>
                </div>
            </div>
            <!-- <div class="pagebreak"></div> -->
            @endforeach
            @endif                 
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/inwards/show.js') }}"></script>
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
