<?php 
$counter = 1;
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <title>{{ config('app.name')}} : Print Inwards</title>
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/reports/inwards-outwards.css') }}">
</head>
<body>    
<div class="ibox">
    <div class="ibox-body">
        @if(isset($customer_orders) && $customer_orders->count()>0)
        <p class="noprint text-right">
            <a class="btn btn-dark" href="javascript:void(0)" onClick="window.print();"><i class="fa fa-print"></i> Print</a>
        </p>
        <h2 class="text-center">Inwards</h2>
        <div class="table-responsive">
            <table class="w-100" border="1">
                <thead>
                    <tr>
                        <td class="w-5 font-weight-bold border-left bg">#</td>
                        <td class="font-weight-bold border-left bg">Serial No.</td>
                        <td class="font-weight-bold border-left bg">Inward Date</td>
                        <!-- <td class="font-weight-bold border-left bg">Customer</td> -->
                        <td class="font-weight-bold border-left bg">From</td>
                        <td class="font-weight-bold border-left bg">Transporter</td>
                        <td class="font-weight-bold border-left bg">Additional Charge</td>
                    </tr>
                </thead>
                <tbody>                    
                @foreach($customer_orders as $customer_order)
                <?php
                    $customer = $customers->where("customer_id",$customer_order->customer_id)->first();
                ?>
                    <tr>
                        <td class="border-left">{{ $counter++ }}</td>
                        <td class="border-left">{{ $customer_order->sr_no }}</td>
                        <td class="border-left">{{ $customer_order->date }}</td>
                        <!-- <td class="border-left">{{ $customer->fullname }}</td> -->                            
                        <td class="border-left">{{ $customer_order->from }}</td>                            
                        <td class="border-left">{{ $customer_order->transporter }}</td>                                                        
                        <td class="border-left">{{ $customer_order->additional_charge }}</td>
                    </tr>
                @endforeach            
                    <tr>
                        <td class="border-right border-bottom-0" colspan="5"></td>                            
                        <td><b>Total:</b></td>
                        <td><b>{{Helper::formatAmount($total_additional_charge)}}</b></td>                            
                    </tr>
                </tbody>
            </table>
        </div> 
        @else
            <div> No records found. </div>            
        @endif
    </div>
</div>
<script src="{{ asset('/assets/app/vendors/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/app/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('/assets/app/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script type="text/javascript">
$(function() {
    window.print();
});
</script>
</body>
</html>
