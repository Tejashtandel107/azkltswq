@extends('web.layouts.app')

@section('pagetitle',$pagetitle)

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/web/css/reports/insurance-reports.css') }}">
@endsection

@section('pagecontent')
<!-- Page Heading Breadcrumbs -->
@include('web.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox mb-2 noprint">
        <div class="ibox-body py-3">
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer_id ?? '' }}">
            <form method="GET" action="{{ route('user.reports.insurance-report.show') }}" id="insurance-form">
                <div id="notify"></div>
                <div class="row">                                  
                    <div class="col-lg-12 col-md-12 col-sm-12 noprint">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-wrap">
                                <div class="mr-2 mb-2" style="width: 270px;">
                                    <label class="pr-1">Date Range:</label>
                                    <div class="input-group date">
                                        <input type="text" name="from" class="form-control datepicker"  placeholder="From" autocomplete="off" value="{{ ($request->from ?? '') }}" required>
                                        <span class="input-group-addon pl-2 pr-2">to</span>
                                        <input type="text" name="to" class="form-control datepicker"  placeholder="To" autocomplete="off" value="{{ ($request->to ?? '') }}" required>
                                    </div>        
                                </div>
                                <div class="mr-2 mb-2">
                                    <label class="pr-1">Item:</label>
                                    <select name="i" class="form-control" onchange="fetchMarka(this)">
                                        <option value="">Select</option>
                                        @foreach($items->pluck('name', 'item_id') as $id => $name)
                                            <option value="{{ $id }}" @selected(request('i') == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mr-2 mb-2">
                                    <label class="pr-1">Marka:</label>
                                    <select name="m" id="marka_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach($markas->pluck('name', 'marka_id') as $id => $name)
                                            <option value="{{ $id }}" @selected(request('m') == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mr-2 mb-2">
                                    <label class="pr-1">Search</label>
                                    <div class="input-group-icon input-group-icon-left">
                                        <span class="input-icon input-icon-right font-16"><i class="ti-search"></i></span>
                                        <input type="text" name="q" value="{{ ($request->q ?? '') }}" class="form-control" placeholder="Vakkal,Item,Marka">
                                    </div>
                                </div>  
                                <div class="mb-2 align-self-end">
                                    <input type="submit" class="btn btn-primary" href="javascript:void(0)" onclick="formsubmit()" value="Filter">  
                                </div>
                            </div>                          
                        </div>                  
                    </div>    
                </div>            
            </form>
        </div>
    </div>   
    <div class="ibox" id="printbox">
        <div class="ibox-body text-right">
            <a class="btn btn-dark noprint" href="javascript:void(0)" onclick="printReport();"><i class="fa fa-print"></i> Print</a>
            <div>
            @if(!count($request->all())>0)
                <div class="text-left mt-3 alert alert-danger has-icon"><i class="la la-warning alert-icon noprint"></i> Please select any filters to generate report. </div>                
            @elseif(isset($results) && ($results->count() > 0))
            @php            
                $full_ledgers = $results->groupBy('customer_id');
            @endphp                 
                @foreach($full_ledgers as $insurance_results)
            @php                
                $customer_info = $insurance_results->first();
            @endphp
                <div class="main-wrapper">
                    <div class="text-center">
                        <div class="text-center top-title ">|| {{$company_info->gods_quotes}} ||</div>
                        <div class="border border-black main-wrapper mb-2">
                            <div class="border-bottom">
                                <div class="text-center font-20 font-weight-bold print-title">Statement Of Insurance</div>
                            </div>
                            <div class="text-center p-2 print-padding border-bottom">
                                <div class="pb-1 font-weight-bold font-20">
                                    {{strtoupper($company_info->companyname)}}
                                </div>
                                <div class="font-weight-bold font-17">
                                    {{strtoupper($company_info->address)}}
                                </div>
                            </div>

                            <table class="w-100">
                                <thead>
                                    <tr class="text-left">
                                        <td colspan="10" class="p-2 print-padding border-right" style="width: 55.8%;">
                                            <div><span class="font-weight-bold">M/S.</span>:&nbsp;&nbsp; {{$customer_info->companyname}}</div>
                                            <div><span class="font-weight-bold">Address</span>:&nbsp;&nbsp;{{$customer_info->address}}</div>
                                        </td>
                                        <td class="p-2 print-padding" colspan="2">
                                            <div class="print-p-b-0"><span class="font-weight-bold">For The Period of :</span>&nbsp;
                                                <span>
                                                    @if($period->count() > 0)
                                                    @php
                                                        $startmonth = $period->getStartDate()->format('F,Y');
                                                        $endmonth = $period->getEndDate()->format('F,Y'); 
                                                    @endphp
                                                        {{ ($startmonth == $endmonth) ? $endmonth : $startmonth .' to '.$endmonth }}
                                                    @else
                                                        NULL
                                                    @endif
                                                </span>                                    
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="w-100" border="1">
                                <thead>
                                    <tr>
                                        <td class="text-center font-weight-bold p-2 header-bg border-left">Vakkal No.</td>
                                        <td class="text-center font-weight-bold header-bg">Item</td>
                                        <td class="text-center font-weight-bold header-bg">Marka</td>
                                        <td class="text-center font-weight-bold header-bg">Quantity</td>
                                        <td class="text-center font-weight-bold header-bg">Total Weight</td>
                                        <td class="text-center font-weight-bold header-bg">Rate</td>
                                        <td class="text-center font-weight-bold header-bg">Valuation</td>
                                        <td class="text-center font-weight-bold header-bg">Month</td>
                                        <td class="text-center font-weight-bold header-bg">Insurance Rate</td>
                                        <td class="text-center font-weight-bold header-bg">Amount</td>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                    $total_valuation = 0;
                                    $total_amount = 0;
                                @endphp    
                                @foreach($insurance_results as $result)
                                 @php
                                    $balance_quantity = $result->inwards - $result->outwards;
                                    $total_weight = $balance_quantity * $result->weight;
                                    $valuation = $total_weight * $result->item_rate;
                                    $amount = ($valuation/100000)*$result->insurance_rate;
                                    $total_valuation += $valuation;
                                    $total_amount += $amount;
                                 @endphp
                                    <tr class="text-center">
                                        <td class="border-left">{{$result->vakkal_number}}</td>
                                        <td>{{$result->item_name}}</td>
                                        <td>{{$result->marka_name}}</td>
                                        <td>{{$balance_quantity}}</td>
                                        <td>{{$total_weight}}</td>
                                        <td>{{$result->item_rate}}</td>
                                        <td>{{$valuation}}</td>
                                        <td>{{$result->Month }} - {{$result->Year}}</td>
                                        <td>{{$result->insurance_rate}}</td>
                                        <td>{{Helper::formatAmount($amount)}}</td>
                                    </tr>
                                @endforeach
                                    <tr class="text-center">
                                        <td class="border-left" colspan="3"></td>
                                        <td class="font-weight-bold">Total</td>
                                        <td class="font-weight-bold header-bg">{{$insurance_results->sum('total_balance_weight')}}</td>
                                        <td></td>
                                        <td class="font-weight-bold header-bg">{{Helper::formatAmount($total_valuation)}}</td>
                                        <td></td>
                                        <td></td>
                                        <td class="font-weight-bold header-bg">{{Helper::formatAmount($total_amount)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>  
                </div>
             @endforeach
            @else
                <div class="text-left mt-3 alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon noprint"></i> No data found to generate report. </div>                
            @endif           
                
            </div>
        </div>
    </div>
</div>

@endsection

@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/web/js/reports/insurance.js') }}"></script>
@endsection