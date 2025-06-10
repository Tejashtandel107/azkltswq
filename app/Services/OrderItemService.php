<?php

namespace App\Services;

use App\Models\OrderItems;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DB;
use Helper;
use Illuminate\Http\Request;

class OrderItemService extends BaseService
{
    protected $model;

    public function __construct(OrderItems $orderitem_obj)
    {
        $this->model = $orderitem_obj;
    }

    public function manageLedger($request)
    {
        $queryInstance = OrderItems::select('c.companyname', 'c.address', 'co.customer_id', 'co.sr_no', 'co.date as order_date', 'order_items.*', 'i.name as item_name', 'm.name as marka_name', 'm.*')
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
            ->leftJoin('customers as c', 'co.customer_id', '=', 'c.customer_id')
            ->leftJoin('items as i', 'i.item_id', '=', 'order_items.item_id')
            ->leftJoin('marka as m', 'm.marka_id', '=', 'order_items.marka_id')
            ->whereNull('order_items.deleted_at');

        $queryInstance->addSelect(DB::raw('(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_inward'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select');
        $queryInstance->addSelect(DB::raw('(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_outward'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');
        $queryInstance->addSelect(DB::raw('(SELECT SUM(IF(oi.type=?,oi.quantity*oi.weight,0))-SUM(IF(oi.type=?,oi.quantity*oi.weight,0)) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort) AS total_balance_weight'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select')->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $dt = Carbon::parse($from);
            $from = $dt->toDateString();

            $queryInstance->where('co.date', '>=', $from);
        } else {
            $from = '';
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $dt = Carbon::parse($to);
            $to = $dt->toDateString();

            $queryInstance->where('co.date', '<=', $to);
        }

        if ($request->filled('c') && $request->input('c') != 'all_customer') {
            $customer_id = $request->input('c');
            $queryInstance->where('co.customer_id', $customer_id);
        }

        if ($request->filled('i')) {
            $item_id = $request->input('i');
            $queryInstance->where('order_items.item_id', $item_id);
        }

        if ($request->filled('m')) {
            $marka_id = $request->input('m');
            $queryInstance->where('order_items.marka_id', $marka_id);
        }

        $keyword = $request->input('q');
        if ($keyword != '') {
            $search_fields = ['i.name', 'm.name', 'order_items.vakkal_number', 'co.sr_no'];
            parent::filterByKeywords($search_fields, $queryInstance, $keyword);
        }
        // Set Order By
        $queryInstance->orderBy('c.companyname')->orderBy('i.name')->orderBy('vakkal_number')->orderBy('order_items.sort');

        if ($request->filled('r')) {
            // Business Logic to get Last record for each vakkal_number
            $results = $queryInstance->get()->groupBy('vakkal_number');
            $collection = collect();
            $r = $request->input('r');
            foreach ($results as $key => $values) {
                $temp = $values->last();
                if ($r == 'finished') {
                    if ($temp->total_balance_weight == 0) {
                        $collection->push($temp);
                    }
                }
                if ($r == 'fresh' || $r == 'current') {
                    if ($temp->total_outward == '') {
                        $collection->push($temp);
                    }
                    if ($r == 'current') {
                        if ($temp->total_balance_weight > 0 && $temp->total_outward > 0) {
                            $collection->push($temp);
                        }
                    }
                }
            }
            $results = null;

            return $collection;
        } else {
            return $queryInstance->get();
        }
    }

    public function calculateCurrentBalance(Request $request)
    {
        $queryInstance = OrderItems::select('order_items.*')
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
            ->whereNull('order_items.deleted_at');

        $queryInstance->addSelect(DB::raw('(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_inward'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select');
        $queryInstance->addSelect(DB::raw('(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_outward'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');
        // $queryInstance->addSelect(DB::raw ("(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_outward"))->addBinding(config ('constant.CUSTOMER_ORDER_TYPE.OUTWARD'),'select');
        $queryInstance->addSelect(DB::raw('(SELECT SUM(IF(oi.type=?,oi.quantity*oi.weight,0))-SUM(IF(oi.type=?,oi.quantity*oi.weight,0)) FROM order_items as oi WHERE oi.deleted_at is null and oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort) AS total_balance_weight'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select')->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $dt = Carbon::parse($from);
            $from = $dt->toDateString();

            $queryInstance->where('co.date', '>=', $from);
        } else {
            $from = '';
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $dt = Carbon::parse($to);
            $to = $dt->toDateString();

            $queryInstance->where('co.date', '<=', $to);
        }

        if ($request->filled('c') && $request->input('c') != 'all_customer') {
            $customer_id = $request->input('c');
            $queryInstance->where('co.customer_id', $customer_id);
        }
        $queryInstance->orderBy('vakkal_number')->orderBy('order_items.sort');

        if ($request->filled('r')) {
            // Business Logic to get Last record for each vakkal_number
            $results = $queryInstance->get()->groupBy('vakkal_number');
            $collection = collect();
            $selections = $request->input('r');
            foreach ($results as $key => $values) {
                $temp = $values->last();
                foreach ($selections as $r) {
                    if ($r == 'finished') {
                        if ($temp->total_balance_weight == 0) {
                            $collection->push($temp);
                        }
                    }
                    if ($r == 'new') {
                        if ($temp->total_outward == '') {
                            $collection->push($temp);
                        }
                    }
                    if ($r == 'final') {
                        if ($temp->total_balance_weight > 0 && $temp->total_outward > 0) {
                            $collection->push($temp);
                        }
                    }
                }
            }
            $results = null;

            return $collection;
        } else {
            return $queryInstance->get();
        }
    }

    public function calculateInwardOutwardWeight(Request $request)
    {
        $queryInstance = OrderItems::select(DB::raw("SUM(IF(order_items.type='inward',order_items.quantity*order_items.weight,0)) as total_inward_weight"), DB::raw("SUM(IF(order_items.type='outward',order_items.quantity*order_items.weight,0)) as total_outward_weight"))
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
            ->whereNull('order_items.deleted_at');

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $dt = Carbon::parse($from);
            $from = $dt->toDateString();

            $queryInstance->where('co.date', '>=', $from);
        } else {
            $from = '';
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $dt = Carbon::parse($to);
            $to = $dt->toDateString();

            $queryInstance->where('co.date', '<=', $to);
        }

        if ($request->filled('c') && $request->input('c') != 'all_customer') {
            $customer_id = $request->input('c');
            $queryInstance->where('co.customer_id', $customer_id);
        }

        return $queryInstance->first();
    }

    public function manageStorageCapacity($request)
    {
        $queryInstance = OrderItems::select('chamber_id', 'floor_id', 'grid_id', DB::raw('sum(IF(type=?, weight*quantity , 0)) AS totalinwards'), DB::raw('sum(IF(type=?, weight*quantity , 0)) AS totaloutwards'))
            ->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select')
            ->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select')
            ->groupBy('chamber_id', 'floor_id', 'grid_id')
            ->orderBy('chamber_id', 'asc')->orderBy('floor_id', 'asc')->orderBy('grid_id', 'asc');

        if ($request->filled('ch') && $request->input('ch') != 'all') {
            $chamber_id = $request->input('ch');
            $queryInstance->where('chamber_id', $chamber_id);
        }

        if ($request->filled('fl') && $request->input('fl') != 'all') {
            $floor_id = $request->input('fl');
            $queryInstance->where('floor_id', $floor_id);
        }

        return $queryInstance;
    }

    public function getOrderInsurance(Request $request, $is_reports = true)
    {

        $from = $request->filled('from') ? Helper::convertDateFormat($request->input('from')) : Carbon::parse('2018-01-01');
        $to = $request->filled('to') ? Helper::convertDateFormat($request->input('to')) : now();

        if ($request->filled('from') && $request->filled('to') && $from->toDateString() > $to->toDateString()) {
            $from = $request->filled('to') ? Helper::convertDateFormat($request->input('to')) : Carbon::parse('2018-01-01');
            $to = $request->filled('from') ? Helper::convertDateFormat($request->input('from')) : now();
        }
        if (! $request->filled('from')) {
            $from = $request->filled('to') ? Helper::convertDateFormat($request->input('to')) : Carbon::parse('2018-01-01');
        }

        $interval = CarbonInterval::createFromDateString('1 months');

        $period = new CarbonPeriod($from, $interval, $to);
        $inner_query_arr = [];

        foreach ($period as $dt) {
            $inner_query_arr[] = 'SELECT LAST_DAY("'.$dt->format('Y-m-d').'") AS merge_date';
        }

        $innerQuery = implode(' UNION ', $inner_query_arr);

        $inner_query_arr = null;
        $queryInstance = DB::table(DB::raw('('.$innerQuery.') as mt'))
            ->select(DB::raw('YEAR(mt.merge_date) AS Year'), DB::raw("DATE_FORMAT(merge_date,'%b') AS Month"), DB::raw('max(oi.item_rate) as item_rate'), DB::raw('max(oi.insurance_rate) as insurance_rate'), 'oi.vakkal_number', 'oi.weight')
            ->leftJoin('customer_orders as co', 'co.date', '<=', 'mt.merge_date')
            ->leftJoin('order_items as oi', 'oi.customer_order_id', '=', 'co.customer_order_id')
            ->leftJoin('customers as c', 'c.customer_id', '=', 'co.customer_id');

        if ($request->filled('c')) {
            $customer_id = $request->input('c');
            $queryInstance->where('co.customer_id', $customer_id);
        }

        $queryInstance->addSelect(DB::raw('SUM(if(oi.type=?,oi.quantity,0)) as inwards'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select');

        if ($is_reports) {
            $queryInstance->addSelect(DB::raw('SUM(if(date_add(merge_date,interval - DAY(merge_date)+3 DAY) >= co.`date` and oi.type=?,oi.quantity,0)) as outwards'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');
            $queryInstance->addSelect(DB::raw('SUM(IF(oi.type=?,oi.quantity*oi.weight,0))-SUM(IF(date_add(merge_date,interval -DAY(merge_date)+1 DAY) > co.`date` and oi.type=?,oi.quantity*oi.weight,0)) as total_balance_weight'))->addBinding([config('constant.CUSTOMER_ORDER_TYPE.INWARD'), config('constant.CUSTOMER_ORDER_TYPE.OUTWARD')], 'select');
        } else {
            $queryInstance->addSelect(DB::raw('SUM(if(date_add(date_add(merge_date,interval - DAY(merge_date)+1 DAY),interval DAY(?) -1 DAY) >= co.`date` and oi.type=?,oi.quantity,0)) as outwards'))->addBinding([$to->format('Y-m-d'), config('constant.CUSTOMER_ORDER_TYPE.OUTWARD')], 'select');
            $queryInstance->addSelect(DB::raw('SUM(IF(oi.type=?,oi.quantity*oi.weight,0))-SUM(IF(date_add(date_add(merge_date,interval - DAY(merge_date)+1 DAY),interval DAY(?) -1 DAY) > co.`date` and oi.type=?,oi.quantity*oi.weight,0)) as total_balance_weight'))->addBinding([config('constant.CUSTOMER_ORDER_TYPE.INWARD'), $to->format('Y-m-d'), config('constant.CUSTOMER_ORDER_TYPE.OUTWARD')], 'select');
        }

        $queryInstance->having(DB::raw('inwards - outwards'), '>', 0)
            ->groupBy('mt.merge_date')
            ->groupBy('oi.vakkal_number');

        return $queryInstance->whereNull('co.deleted_at')->whereNull('oi.deleted_at')->orderBy('vakkal_number')->orderBy('mt.merge_date')->get();
    }

    public function calculateStorageCharge($id, $type, Request $request)
    {
        $queryInstance = OrderItems::select('order_items.*')
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id');

        if ($id > 0) {
            $queryInstance->where('co.customer_order_id', '=', $id);
        }
        if (! empty($type)) {
            $queryInstance->where('co.type', $type);
        }
        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $queryInstance->whereDate('co.date', '>=', $from);
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $queryInstance->whereDate('co.date', '<=', $to);
        }
        if ($request->filled('c') && $request->input('c') != 'all_customer') {
            $customer_id = $request->input('c');
            $queryInstance->where('co.customer_id', $customer_id);
        }

        // ->orderBy('order_items.sort', 'ASC');
        return $queryInstance->orderBy('order_items.order_item_id', 'ASC');
    }

    public function getOutstandingPayment(Request $request)
    {
        $queryInstance = OrderItems::select('order_items.*', 'c.companyname', 'c.invoice_limit', 'c.last_invoice_date', DB::raw('sum((order_items.rate / 30) * order_items.no_of_days  * order_items.weight * order_items.quantity) as total_storage_charge'), 'co.customer_id')
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
            ->leftJoin('customers as c', 'c.customer_id', '=', 'co.customer_id')
            ->whereRaw('date(co.date) >= c.last_invoice_date')
            ->havingRaw('total_storage_charge >= c.invoice_limit');

        $queryInstance->addSelect(DB::raw('(SELECT SUM(additional_charge) FROM customer_orders WHERE customer_orders.deleted_at is null and customer_orders.customer_id = c.customer_id and date(customer_orders.date) >= c.last_invoice_date and customer_orders.type=?) AS total_inward_additional_charge'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'),'select');
        $queryInstance->addSelect(DB::raw('(SELECT SUM(additional_charge) FROM customer_orders WHERE customer_orders.deleted_at is null and customer_orders.customer_id = c.customer_id and date(customer_orders.date) >= c.last_invoice_date and customer_orders.type=?) AS total_outward_additional_charge'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'),'select');

        return $queryInstance->groupBy('co.customer_id')->orderBy('total_storage_charge', 'DESC')->get();

    }
}
