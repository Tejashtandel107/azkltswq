@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)

@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/reports/stocks-reports.css') }}">
@endsection

@section('pagecontent')
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="ibox mb-2 noprint">
        <div class="ibox-body py-3" style="color: #000">
            <form action="{{ route('admin.reports.stock-report.show') }}" method="get" id="form-filter">
                <div class="flexbox">
                    <div class="flexbox noprint">
                        <div class="form-group mr-2">                        
                            <label class="pr-1">Chamber:</label>
                            <select name="ch" class="form-control" onchange="formsubmit()">
                                <option value="">Select Chamber</option>
                                <option value="all" @selected(request('ch') == 'all')>View All</option>
                                @foreach($chambers as $key => $value)
                                    <option value="{{ $key }}" @selected(request('ch') == $key)>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">                        
                            <label class="pr-1">Floor:</label>
                            <select name="fl" class="form-control" onchange="formsubmit()">
                                <option value="">Select Floor</option>
                                <option value="all" @selected(request('fl') == 'all')>View All</option>
                                @foreach($floors as $key => $value)
                                    <option value="{{ $key }}" @selected(request('fl') == $key)>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>                
                </div>
            </form>
        </div>
    </div>
    <div class="ibox" id="printbox">
        <div class="ibox-body" style="color: #000">
            <div class="noprint text-right">
                    <a class="btn btn-dark" href="javascript:void(0)" onclick="printReport();"><i class="fa fa-print"></i> Print</a>
            </div>                                                                
            <div class="text-center top-title">|| {{$company_info->gods_quotes}} ||</div>
            <div class="border border-black d-flex align-items-center justify-content-between p-2 print-padding">
              <div class="col">GSTIN: {{$company_info->gstnumber}}</div>
              <div class="col text-center font-weight-bold font-20 print-title">Storage Capacity</div>
              <div class="col text-right">Contact: {{$company_info->phone}}</div>
            </div> 
            <div class="text-center p-3 border-bottom border-right border-left print-padding">
                <div class="pb-1 font-weight-bold font-20 print-title">
                    {{strtoupper($company_info->companyname)}}
                </div>
                <div>
                    {{$company_info->address}}
                </div>
            </div>
    @if(($request->filled('ch')) || ($request->filled('fl')))    
        @php
            $end_chamber_loop = (($request->filled('ch')) && $request->input('ch') !="all") ? $request->input('ch') : count($chambers);
            $end_floor_loop = (($request->filled('fl')) && $request->input('fl') !="all") ? $request->input('fl') : count($floors);
            $start_chamber_loop = (($request->filled('ch')) && $request->input('ch') !="all") ? $request->input('ch') : 1 ;
            $start_floor_loop = (($request->filled('fl')) && $request->input('fl') != "all") ? $request->input('fl') : 1 ;
            $count = 1;
        @endphp
        <div class="d-block">
         @for($chamber=$start_chamber_loop; $chamber<=$end_chamber_loop; $chamber++)
            @for($floor=$start_floor_loop; $floor <=  $end_floor_loop; $floor++)
                <div class="d-inline-block" style="width: 100%;">
                    <div class="border-right border-bottom border-top my-3">
                        <div>
                            <div class="header-bg p-2 font-weight-bold text-center border-bottom border-left">Chamber - {{$chambers[$chamber]}} Floor - {{$floors[$floor]}}</div>
                            <div class="d-flex font-weight-bold border-left justify-content-between">
                                <div class="pl-2">Total</div>
                            @php 
                                $totalinwards_quantity = $results->where('chamber_id',$chamber)->where('floor_id',$floor)->sum('totalinwards');
                                $totaloutwards_quantity = $results->where('chamber_id',$chamber)->where('floor_id',$floor)->sum('totaloutwards');                                
                            @endphp    
                                <div class="pr-2"> {{ $totalinwards_quantity - $totaloutwards_quantity }}</div>
                            </div>
                        </div>
                        <div style = "display: grid; grid: repeat(5, 30px) / auto-flow;">
                    @for($grid=count($grids); $grid>=1; $grid--)
                        @php 
                            $result = $results->where('chamber_id',$chamber)->where('floor_id',$floor)->where('grid_id',$grid)->first();   
                            $remaining_quantity = (isset($result->totalinwards) ? $result->totalinwards : 0) - (isset($result->totaloutwards) ? $result->totaloutwards : 0);                                    ;
                        @endphp
                            <div class="d-flex text-center border-left border-top">
                                <div class="flex-grow align-self-center">{{ $remaining_quantity }}</div>
                                <div class="header-bg w-30" style="line-height: 30px"><span class="px-1">G{{ Helper::getGrigNumber($grids[$grid]) }}</span></div>    
                            </div>  
                    @endfor     
                        </div>
                    </div>
                </div>
            @if($count%2==0)
                <div class="pagebreak"><!-- --></div>
            @endif
                @php    
                    $count++
                @endphp
            @endfor
        @endfor
        </div>
    @else
        <div class="mt-3 alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon noprint"></i> No data found to generate report. </div>
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
