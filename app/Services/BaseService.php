<?php

namespace App\Services;

use App\Models\Chamber;
use App\Models\Customer;
use App\Models\CustomerOrders;
use App\Models\Floor;
use App\Models\Grid;
use App\Models\Item;
use App\Models\Marka;
use App\Models\OrderItems;
use App\Models\Setting;
use Helper;
use Illuminate\Http\Request;

abstract class BaseService
{
    public function find($key, $id)
    {
        return $this->model->where($key, $id);
    }

    public function findWithTrashed($key, $id)
    {
        return $this->model->withTrashed()->where($key, $id);
    }

    public function select($key = '', $orderBy = 'asc')
    {
        if (! empty($key)) {
            return $this->model->orderBy($key, $orderBy)->get()->toArray();
        }

        return false;
    }

    public function store($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $result = $this->model->withTrashed()->find($id);
        if ($result) {
            $result->fill($request);

            return $result->save();
        }

        return false;
    }

    public function destroy($id, $key = '')
    {
        if (! empty($key)) {
            return $this->model->where($key, $id)->delete();
        }

        return $this->model->find($id)->delete();
    }

    public function forceDestroy($id, $key = '')
    {
        if (! empty($key)) {
            return $this->model->withTrashed()->where($key, $id)->forceDelete();
        }

        return $this->model->withTrashed()->find($id)->forceDelete();
    }

    public function restoreOrder($id, $key = '')
    {
        if (! empty($key)) {
            return $this->model->withTrashed()->where($key, $id)->restore();
        }

        return $this->model->withTrashed()->find($id)->restore();
    }

    public function filterByKeywords($search_fields, $queryInstance, $keyword = '')
    {
        return $queryInstance->Where(function ($query) use ($keyword, $search_fields) {
            $words = explode(' ', $keyword);
            foreach ($search_fields as $field) {
                $query->orWhere(function ($query) use ($words, $field) {
                    foreach ($words as $word) {
                        $query->Where($field, 'like', '%'.$word.'%');
                    }
                });
            }
        });
    }

    public function getCustomerOrder($type = '', $id = 0)
    {
        $queryInstance = CustomerOrders::select('customer_orders.*', 'c.*', 'c.address as customer_add', 'customer_orders.address as delivery_address', 'u.username as username', 'customer_orders.created_at as order_created_date')
            ->leftJoin('customers as c', 'c.customer_id', '=', 'customer_orders.customer_id')
            ->leftJoin('users as u', 'u.user_id', '=', 'customer_orders.user_id')
            ->where('customer_orders.type', $type);

        if ($id > 0) {
            $queryInstance->where('customer_orders.customer_order_id', '=', $id);
        }

        return $queryInstance;
    }

    public function getCompanyDetails($key = '')
    {
        $queryInstance = Setting::where('key', $key)->first();

        return json_decode($queryInstance->value);
    }

    public function getOrderItems($id, $type, Request $request)
    {
        $queryInstance = OrderItems::select('order_items.*', 'i.name as item_name', 'm.name as marka_name', 'f.number as floor_number', 'g.number as grid_number', 'c.number as chamber_number')
            ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
            ->leftJoin('items as i', 'i.item_id', '=', 'order_items.item_id')
            ->leftJoin('floor as f', 'f.floor_id', '=', 'order_items.floor_id')
            ->leftJoin('grid as g', 'g.grid_id', '=', 'order_items.grid_id')
            ->leftJoin('chamber as c', 'c.chamber_id', '=', 'order_items.chamber_id')
            ->leftJoin('marka as m', 'm.marka_id', '=', 'order_items.marka_id');
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

    public function pluckData($key = [])
    {
        if (in_array('item_id', $key)) {
            $items = Item::withTrashed()->Active()->orderBy('name', 'asc')->get()->pluck('name', 'item_id')->toArray();
        } else {
            $items = [];
        }

        if (in_array('marka_id', $key)) {
            $markas = Marka::withTrashed()->orderBy('name', 'asc')->get()->pluck('name', 'marka_id')->toArray();
        } else {
            $markas = [];
        }

        if (in_array('chamber_id', $key)) {
            $chambers = Chamber::orderBy('sort', 'asc')->get()->pluck('number', 'chamber_id')->toArray();
        } else {
            $chambers = [];
        }

        if (in_array('floor_id', $key)) {
            $floors = Floor::orderBy('sort', 'asc')->get()->pluck('number', 'floor_id')->toArray();
        } else {
            $floors = [];
        }

        if (in_array('grid_id', $key)) {
            $grids = Grid::orderBy('sort', 'asc')->get()->pluck('number', 'grid_id')->toArray();
        } else {
            $grids = [];
        }

        if (in_array('customer_id', $key)) {
            $customers = Customer::withTrashed()->Active()->orderBy('companyname', 'asc')->get()->pluck('companyname', 'customer_id')->toArray();
        } else {
            $customers = [];
        }

        return [$items, $markas, $chambers, $floors, $grids, $customers];
    }

    public function getMaxSerialNo($type)
    {
        // $serial_number = CustomerOrders::where('type',$type)->max('sr_no');
        $CustomerOrder = CustomerOrders::withTrashed()->where('type', $type)->latest()->first();

        return $CustomerOrder->sr_no + 1;
    }

    public function fetchCustomerItems($customer_id = '')
    {
        $items = Item::leftjoin('order_items as oi', 'items.item_id', '=', 'oi.item_id')
            ->leftjoin('customer_orders as co', 'oi.customer_order_id', '=', 'co.customer_order_id')
            ->where('co.customer_id', $customer_id)
            ->withTrashed()->orderBy('name')->groupBy('oi.item_id')->get();

        return $items;
    }

    public function fetchCustomerMarka($item_id = '', $customer_id = '')
    {
        $markas = Marka::leftjoin('order_items as oi','oi.marka_id','=','marka.marka_id')
            ->leftjoin('customer_orders as co','co.customer_order_id','=','oi.customer_order_id')
            ->withTrashed()->where('co.customer_id',$customer_id)
            ->where('marka.item_id',$item_id)->groupBy('oi.marka_id')->orderBy('name')->get();

        return $markas;
    }
}
