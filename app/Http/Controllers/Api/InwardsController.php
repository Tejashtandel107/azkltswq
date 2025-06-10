<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marka;
use App\Models\OrderItems;
use App\Services\OrderItemService;
use DB;
use Helper;
use Illuminate\Http\Request;

class InwardsController extends Controller
{
    protected $orderitem_obj;

    public function __construct(OrderItemService $orderitem)
    {
        $this->orderitem_obj = $orderitem;
    }

    /**
     * Search Inward Entry from order_items table,
     * filters available: item_id, marka_id, keyword, date from, date to .
     */
    public function Search(Request $request)
    {
        if ($request->ajax()) {

            $item_id = $request->data['item'];
            $marka_id = $request->data['marka'];
            $search = $request->data['search'];
            $customer_id = $request->data['customer_id'];
            $from = $request->data['from'] ? Helper::convertDateFormat($request->data['from']) : null;
            $to = $request->data['to'] ? Helper::convertDateFormat($request->data['to']) : null;

            if (isset($item_id) || isset($marka_id) || isset($from) || isset($to) || ! empty($search)) {
                $queryInstance = OrderItems::select('vakkal_number', 'co.date', 'c.companyname', 'i.name as item_name', 'order_item_id', 'm.name as marka_name', 'quantity', 'chamber.number as chamber_number', 'floor.number as floor_number', 'grid.number as grid_number', 'order_items.description')
                    ->leftJoin('customer_orders as co', 'order_items.customer_order_id', '=', 'co.customer_order_id')
                    ->leftJoin('customers as c', 'co.customer_id', '=', 'c.customer_id')
                    ->leftJoin('items as i', 'order_items.item_id', '=', 'i.item_id')
                    ->leftJoin('marka as m', 'order_items.marka_id', '=', 'm.marka_id')
                    ->leftJoin('chamber', 'order_items.chamber_id', '=', 'chamber.chamber_id')
                    ->leftJoin('floor', 'order_items.floor_id', '=', 'floor.floor_id')
                    ->leftJoin('grid', 'order_items.grid_id', '=', 'grid.grid_id')
                    ->where('order_items.type', config('constant.CUSTOMER_ORDER_TYPE.INWARD'));

                $queryInstance->addSelect(DB::raw("(SELECT SUM(if(oi.type='inward',oi.quantity,0))-SUM(if(oi.type='outward',oi.quantity,0)) FROM order_items as oi WHERE oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.chamber_id=order_items.chamber_id and oi.floor_id=order_items.floor_id and oi.grid_id=order_items.grid_id and oi.deleted_at is null) AS balance_quantity"));
                // $queryInstance->addSelect(DB::raw ("(SELECT SUM(oi.quantity) FROM order_items as oi WHERE oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.type=?) AS total_outward"))->addBinding(config ('constant.CUSTOMER_ORDER_TYPE.OUTWARD'),'select');
                if (isset($item_id)) {
                    $queryInstance->where('order_items.item_id', $item_id);
                }
                if (isset($marka_id)) {
                    $queryInstance->where('order_items.marka_id', $marka_id);
                }
                if (isset($from) && isset($to)) {
                    $queryInstance->whereDate('co.date', '>=', $from)->whereDate('co.date', '<=', $to);
                }
                if ($search != '') {
                    $search_fields = ['vakkal_number'];
                    $this->orderitem_obj->filterByKeywords($search_fields, $queryInstance, $search);
                }
                if (isset($customer_id)) {
                    $queryInstance->where('co.customer_id', $customer_id);
                }
                $results = $queryInstance->orderBy('i.name')->orderBy('m.name')->orderBy('vakkal_number')->orderBy('order_items.sort')->get();
            } else {
                $results = collect();
            }
        }

        return view('admin.outwards.includes.list')->with(compact('results'));
    }

    /**
     * get Inward by order_item_id
     *
     * @return \Illuminate\Http\Response
     */
    public function getInward(Request $request, Marka $obj_marka)
    {
        $order_item_id = $request->filled('order_item_id') ? $request->order_item_id : 0;
        $outward_order_date = $request->outward_order_date;

        $order_items = OrderItems::select('order_items.*', 'co.date')
            ->leftJoin('customer_orders as co', 'order_items.customer_order_id', '=', 'co.customer_order_id')
            ->where('order_items.type', config('constant.CUSTOMER_ORDER_TYPE.INWARD'))->where('order_item_id', $order_item_id)->get();

        if (isset($order_items)) {
            [$items, , $chambers, $floors, $grids] = $this->orderitem_obj->pluckData(['item_id', 'chamber_id', 'floor_id', 'grid_id']);
            $item_marka = $obj_marka->getMarkaByItemId($order_items->pluck('item_id')->toArray());
            if (! empty($item_marka)) {
                $item_marka = $item_marka->groupBy('item_id');
            } else {
                $item_marka = collect();
            }

            return view('admin.outwards.includes.template')->with(compact('items', 'chambers', 'grids', 'floors', 'order_items', 'item_marka', 'outward_order_date'));
        } else {
            return response()->json(['status' => 404]);
        }
    }
}
