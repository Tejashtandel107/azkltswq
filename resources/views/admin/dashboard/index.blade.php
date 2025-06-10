@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
     <style type="text/css">
        .badge-pink[href]:focus, .badge-pink[href]:hover {
          color: #fff;
          background-color: #ff2770;
          border-color: #ff2770;
          -webkit-box-shadow: none;
          box-shadow: none;
          background-image: none;
        }
        .flex-grow-1{
          flex-grow: 1;
        }
        .border-bottom-1{
          border-bottom: 1px solid #e1eaec;
        }
        
    </style>
@endsection

@section('pagetitle',"Dashboard")

@section('pagecontent')
<!-- START PAGE CONTENT-->
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div id="notify"></div>
    <div class="row">
      <div class="col-lg-7">
          <div class="card mb-3">
            <div class="card-body px-0 py-2">
                <div class="d-flex flex-wrap px-3 justify-content-between  align-items-center">
                  <div>
                    <form action="{{ route('admin.dashboard') }}" method="GET" id="form-filter" autocomplete="off">
                      <div class="d-flex flex-wrap  align-items-center">
                            <div class="mb-2 mr-2">
                                <label class="pr-1">Date Range:</label>
                                <div class="input-group date">
                                  <input type="text" name="from" value="{{ $request->from ?? Helper::DateFormat(now()->firstOfMonth(), config('constant.DATE_FORMAT_SHORT')) }}" class="form-control datepicker w-150" placeholder="From" />
                                    <span class="input-group-addon pl-2 pr-2">to</span>
                                    <input type="text" name="to" class="form-control datepicker w-150" placeholder="To" value="{{ $request->to ?? Helper::DateFormat(today(), config('constant.DATE_FORMAT_SHORT')) }}">
                                </div>
                          </div>
                          <div class="mr-2 mb-2">
                            <label class="pr-1">Customer:</label>
                           <select name="c" class="form-control">
                              <option value="">{{ __('All Customers') }}</option>
                              @foreach ($customers as $customer)
                                  <option value="{{ $customer->customer_id }}" 
                                      {{ (isset($request->c) && $request->c == $customer->customer_id) ? 'selected' : '' }}>
                                      {{ $customer->fullname }}
                                  </option>
                              @endforeach 
                            </select>
                          </div>
                          <div class="mb-2 align-self-end mr-2">
                              <a class="btn btn-primary" href="javascript:void(0)" onclick="formsubmit('{{route('admin.dashboard')}}')">Get Report</a>
                          </div>
                      </div>
                    </form>
                  </div> 
                </div>    
            </div>
        </div>
        <div id="dashboard-main">
        
        </div>
      </div>
      <div class="col-lg-5">
        <div id="dashboard-left">
        
        </div>
      </div>
    </div>
</div>
@endsection

@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>    
    <script src="{{ asset('/assets/app/vendors/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>    
    
@endsection

@section('page-scripts')
    <script type="text/javascript">
      function formsubmit(url){
        var formdata = $("#form-filter").serialize();
        ajaxFetch(url,formdata,formSubmitResponse,formSubmitErrorResponse);
      }
      function formSubmitResponse(response, status){
        hideLoader();
        $("#dashboard-main").html(response);
        $('.datepicker').each(function(){
            bindDatePicker($(this));
        });
      }
      function formOutStandingsubmit(url){
        var formdata = $("#form-filter").serialize();
        ajaxFetch(url,formdata,formOutStandingResponse,formSubmitErrorResponse);
      }
      function formOutStandingResponse(response, status){
        hideLoader();
        $("#dashboard-left").html(response);
      }
      function binddatepicker() {
          $('.datepicker').each(function(){
              bindDatePicker($(this));
          });
      }
      function formSubmitErrorResponse(XMLHttpRequest, textStatus, errorThrown){
        hideLoader();
        $("#notify").notification({caption: 'Sorry, We have encountered an error while processing your request. Please try again after some time.', type:'error', sticky:true});
      }
      $(function () {
          formsubmit("<?php echo route('admin.dashboard');?>");
          formOutStandingsubmit("<?php echo route('admin.outstanding-payments');?>");
          binddatepicker();
      });
    </script>
@endsection    
