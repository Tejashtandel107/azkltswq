<?php

namespace App\Services;

use App\Models\CustomerOrders;
use DB;
use Helper;
use Illuminate\Http\Request;

class CustomerOrderService extends BaseService
{
    protected $model;

    public function __construct(CustomerOrders $customerorder_obj)
    {
        $this->model = $customerorder_obj;
    }

    public function filterListByType($request, $type = '')
    {
        $queryInstance = parent::getCustomerOrder($type);

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $queryInstance->whereDate('customer_orders.date', '>=', $from);
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $queryInstance->whereDate('customer_orders.date', '<=', $to);
        }

        if ($request->filled('customer_id') && $request->input('customer_id') != 'all_customer') {
            $customer_id = $request->input('customer_id');
            $queryInstance->where('customer_orders.customer_id', $customer_id);
        }

        if ($request->filled('s')) {
            $sr_no = $request->input('s');
            $queryInstance->where('customer_orders.sr_no', $sr_no);
        }

        if ($request->filled('f')) {
            $sr_no = $request->input('f');
            $queryInstance->where('customer_orders.additional_charge', '!=', 0);
        }

        return $queryInstance;
    }

    public function calculateAdditionalCharge(Request $request)
    {
        $queryInstance = $this->model->select(DB::raw("SUM(IF(customer_orders.type ='inward', additional_charge, 0)) as total_inward_additional_charge"), DB::raw("SUM(IF(customer_orders.type ='outward', additional_charge, 0)) as total_outward_additional_charge"), DB::raw('SUM(additional_charge) as total_additional_charge'));

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $queryInstance->whereDate('customer_orders.date', '>=', $from);
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $queryInstance->whereDate('customer_orders.date', '<=', $to);
        }
        if ($request->filled('c') && $request->input('c') != 'all_customer') {
            $customer_id = $request->input('c');
            $queryInstance->where('customer_orders.customer_id', $customer_id);
        }

        return $queryInstance->first();
    }
}
