<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerOrdersRequest;
use App\Models\Customer;
use App\Models\CustomerOrders;
use App\Models\Marka;
use App\Models\OrderItems;
use App\Services\CustomerOrderService;
use App\Services\OrderItemService;
use Auth;
use DB;
use Illuminate\Http\Request;

class OutwardsController extends Controller
{
    protected $customerorder_obj;

    protected $orderitem_obj;

    public function __construct(CustomerOrderService $customerorder, OrderItemService $orderitem)
    {
        $this->customerorder_obj = $customerorder;
        $this->orderitem_obj = $orderitem;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'pagetitle' => 'All Outward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Outwards' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'alloutward',
        ];

        $show = $request->filled('p') ? $request->input('p') : 100;
        $customers = Customer::withTrashed()->orderBy('companyname')->get();
        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->orderBy('customer_order_id', 'desc')->Paginate($show);

        return view('admin.outwards.index', $data)->with(compact('customer_orders', 'request', 'customers', 'show', 'total_additional_charge'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Outward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Outwards' => route('admin.outwards.index'), 'Create' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'outward',
        ];

        [$items, , $chambers, $floors, $grids] = $this->customerorder_obj->pluckData(['item_id', 'chamber_id', 'floor_id', 'grid_id']);

        $serial_no = $this->customerorder_obj->getMaxSerialNo('outward');

        $customers = Customer::Active()->orderBy('companyname', 'asc')->get();

        return view('admin.outwards.create', $data)->with(compact('customers', 'items', 'chambers', 'grids', 'floors', 'serial_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerOrdersRequest $request)
    {
        $type = 'error';
        $message = 'Sorry, failed to add Outward Entry. Please try again.';
        $request_arr = $request->all();
        $user = Auth::User();
        $request_arr['user_id'] = $user->user_id;
        $request_arr['type'] = config('constant.CUSTOMER_ORDER_TYPE.OUTWARD');
        $obj_order_items = new OrderItems;

        // store customer orders into DB
        $result_customer_order = $this->customerorder_obj->store($request_arr);
        if ($result_customer_order) {
            // store customer orders into DB
            $result = $obj_order_items->addOrderItems($result_customer_order['customer_order_id'], $request_arr, $request_arr['type']);
            if ($result->success) {
                $type = 'success';
                $message = 'Outward Entry added successfully.';
            } else {
                $customer_orders = $this->customerorder_obj->forceDestroy($result_customer_order['customer_order_id']);
                $type = $result->type;
                $message = $result->message;

                return response()->json(['type' => $type, 'message' => $message]);
            }

            $obj_order_items->sortRecords($request_arr['vakkal_number']);
        }

        return response()->json(['type' => $type, 'message' => $message, 'redirect' => route('admin.outwards.showReceipt', $result_customer_order['customer_order_id'])]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Display Receipt for outward entries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showReceipt(Request $request, $id)
    {
        $data = [
            'pagetitle' => 'Outward Receipt',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Outwards' => route('admin.outwards.index'), 'Receipt' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'alloutward',
        ];
        $user = Auth::User();

        $company_info = $this->customerorder_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));

        $customer_order = $this->customerorder_obj->getCustomerOrder(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), $id)->withTrashed()->first();
        $queryInstance = $this->orderitem_obj->getOrderItems($id, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), $request);

        $queryInstance->addSelect(DB::raw('(SELECT sum(if(oi.type=?,oi.quantity,0))-sum(if(oi.type=?,oi.quantity,0)) FROM order_items as oi WHERE oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort and oi.deleted_at is null) AS balance_quantity'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select')->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');

        $outward_items = $queryInstance->withTrashed()->get();

        if (empty($customer_order)) {
            abort(403);
        }

        return view('admin.outwards.show-receipt', $data)->with(compact('customer_order', 'outward_items', 'user', 'company_info'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Marka $obj_marka, $id)
    {
        $customer_order = CustomerOrders::withTrashed()->findOrFail($id);
        $data = [
            'pagetitle' => 'Edit Outward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Outwards' => route('admin.outwards.index'), 'Edit' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'outward',
        ];

        [$items, , $chambers, $floors, $grids] = $this->customerorder_obj->pluckData(['item_id', 'chamber_id', 'floor_id', 'grid_id']);
        $customers = Customer::withTrashed()->Active()->orderBy('companyname', 'asc')->get();
        $order_items = OrderItems::withTrashed()->Outward()->where('customer_order_id', $id)->orderBy('order_item_id', 'ASC')->get();
        // dump($order_items);
        $item_marka = $obj_marka->getMarkaByItemId($order_items->pluck('item_id')->toArray());
        if (! empty($item_marka)) {
            $item_marka = $item_marka->groupBy('item_id');
        } else {
            $item_marka = collect();
        }

        return view('admin.outwards.create', $data)->with(compact('customers', 'items', 'chambers', 'grids', 'floors', 'order_items', 'customer_order', 'request', 'item_marka'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerOrdersRequest $request, $id)
    {

        $type = 'error';
        $message = 'Sorry, failed to update Outward Entry. Please try again.';
        $request_arr = $request->all();
        $obj_customer_orders = CustomerOrders::withTrashed()->findOrFail($id);
        $request_arr['deleted_at'] = $obj_customer_orders['deleted_at'];
        $obj_order_items = new OrderItems;

        // update customer orders into DB
        if ($this->customerorder_obj->update($id, $request_arr)) {
            // update order items into DB
            $result = $obj_order_items->updateOrderItems($id, $request_arr, $obj_customer_orders['type']);
            if ($result->success) {
                $type = 'success';
                $message = 'Outward Entry updated successfully.';
            } else {
                $type = $result->type;
                $message = $result->message;
            }

            $obj_order_items->sortRecords($request_arr['vakkal_number']);
        }
        if ($request->printoutward == 1) {
            $redirect_location = route('admin.outwards.showReceipt', $id);
        } else {
            $redirect_location = route('admin.outwards.index');
        }

        return response()->json(['type' => $type, 'message' => $message, 'redirect' => $redirect_location]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to delete Outward Entry. Please try again.';

            // delete records from customer_orders table
            if ($this->customerorder_obj->destroy($id)) {
                $user = Auth::User();
                $request_arr['deleted_user_id'] = $user['user_id'];
                $this->customerorder_obj->update($id, $request_arr);

                $type = 'success';
                $message = 'Outward Entry deleted successfully.';

                // delete records from order_items table
                if ($this->orderitem_obj->destroy($id, 'customer_order_id')) {
                    $type = 'success';
                    $message = 'Outward Entry deleted successfully.';
                }
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }

    /**
     * Print list of Inwards.
     */
    public function print(Request $request)
    {
        $customers = Customer::withTrashed()->orderBy('companyname')->get();
        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date','desc')->orderBy('sr_no','desc')->get();

        return view('admin.outwards.print')->with(compact('customer_orders','request','customers','total_additional_charge'));
    }
}
