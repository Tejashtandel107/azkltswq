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
use Illuminate\Http\Request;

class InwardsController extends Controller
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
            'pagetitle' => 'All Inward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Inwards' => ''],
            'menuParent' => 'inwards',
            'menuChild' => 'allinward',
        ];
        $show = $request->filled('p') ? $request->input('p') : 100;

        $customers = Customer::withTrashed()->orderBy('companyname')->get();
        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.INWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->orderBy('customer_order_id', 'desc')->Paginate($show);

        return view('admin.inwards.index', $data)->with(compact('customer_orders', 'request', 'customers', 'show', 'total_additional_charge'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Inward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Inwards' => route('admin.inwards.index'), 'Create' => ''],
            'menuParent' => 'inwards',
            'menuChild' => 'inward',
        ];

        [$items, $markas, $chambers, $floors, $grids] = $this->customerorder_obj->pluckData(['item_id', 'marka_id', 'chamber_id', 'floor_id', 'grid_id']);

        $serial_no = $this->customerorder_obj->getMaxSerialNo('inward');

        $customers = Customer::Active()->orderBy('companyname', 'asc')->get();

        return view('admin.inwards.create', $data)->with(compact('customers', 'items', 'chambers', 'grids', 'floors', 'markas', 'serial_no'));
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
        $message = 'Sorry, failed to add Inward Entry. Please try again.';
        $request_arr = $request->all();
        $user = Auth::user();

        $request_arr['user_id'] = $user->user_id;

        $request_arr['type'] = config('constant.CUSTOMER_ORDER_TYPE.INWARD');

        $obj_order_items = new OrderItems;

        // store customer orders into DB
        $result_customer_order = $this->customerorder_obj->store($request_arr);
        if ($result_customer_order) {
            // store customer orders into DB
            $result = $obj_order_items->addOrderItems($result_customer_order['customer_order_id'], $request_arr, $request_arr['type']);
            if ($result->success) {
                $type = 'success';
                $message = 'Inward Entry added successfully.';
            } else {
                $customer_orders = $this->customerorder_obj->destroy($result_customer_order['customer_order_id']);
                $type = $result->type;
                $message = $result->message;
            }

            $obj_order_items->sortRecords($request_arr['vakkal_number']);
        }

        return response()->json(['type' => $type, 'message' => $message, 'redirect' => route('admin.inwards.showReceipt', $result_customer_order['customer_order_id'])]);
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
     * Display Receipt for inward entries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showReceipt(Request $request, $id)
    {
        $data = [
            'pagetitle' => 'Inward Receipt',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Inwards' => route('admin.inwards.index'), 'Receipt' => ''],
            'menuParent' => 'inwards',
            'menuChild' => 'allinward',
        ];
        $user = Auth::user();
        $company_info = $this->customerorder_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));

        $customer_order = $this->customerorder_obj->getCustomerOrder(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), $id)->withTrashed()->first();
        $order_items = $this->orderitem_obj->getOrderItems($id, config('constant.CUSTOMER_ORDER_TYPE.INWARD'), $request)->withTrashed()->get();

        if (empty($customer_order)) {
            abort(403);
        }

        return view('admin.inwards.show-receipt', $data)->with(compact('customer_order', 'order_items', 'user', 'company_info'));
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
            'pagetitle' => 'Edit Inward',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Inwards' => route('admin.inwards.index'), 'Edit' => ''],
            'menuParent' => 'inwards',
            'menuChild' => 'inward',
        ];

        [$items, $markas, $chambers, $floors, $grids] = $this->customerorder_obj->pluckData(['item_id', 'marka_id', 'chamber_id', 'floor_id', 'grid_id']);
        $customers = Customer::withTrashed()->Active()->orderBy('companyname', 'asc')->get();
        $order_items = OrderItems::withTrashed()->Inward()->where('customer_order_id', $id)->orderBy('order_item_id', 'ASC')->get();
        $item_marka = $obj_marka->getMarkaByItemId($order_items->pluck('item_id')->toArray());
        if (! empty($item_marka)) {
            $item_marka = $item_marka->groupBy('item_id');
        } else {
            $item_marka = collect();
        }

        return view('admin.inwards.create', $data)->with(compact('customers', 'items', 'chambers', 'grids', 'floors', 'order_items', 'markas', 'customer_order', 'item_marka'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customer_order_id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerOrdersRequest $request, $customer_order_id)
    {
        $type = 'error';
        $message = 'Sorry, failed to update Inward Entry. Please try again.';
        $request_arr = $request->all();

        $obj_customer_orders = CustomerOrders::withTrashed()->findOrFail($customer_order_id);
        $request_arr['deleted_at'] = $obj_customer_orders['deleted_at'];
        $obj_order_items = new OrderItems;

        // update customer orders into DB
        if ($this->customerorder_obj->update($customer_order_id, $request_arr)) {
            // update order items into DB
            $result = $obj_order_items->updateOrderItems($customer_order_id, $request_arr, $obj_customer_orders->type);
            if ($result->success) {
                $type = 'success';
                $message = 'Inward Entry updated successfully.';
            } else {
                $type = $result->type;
                $message = $result->message;
            }

            $obj_order_items->sortRecords($request_arr['vakkal_number']);
        }

        if ($request->printinward == 1) {
            $redirect_location = route('admin.inwards.showReceipt', $customer_order_id);
        } else {
            $redirect_location = route('admin.inwards.index');
        }

        return response()->json(['type' => $type, 'message' => $message, 'redirect' => $redirect_location]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to delete Inward Entry. Please try again.';

            // delete records from customer_orders table
            if ($this->customerorder_obj->destroy($id)) {
                $user = Auth::User();

                $request_arr['deleted_user_id'] = $user['user_id'];

                $this->customerorder_obj->update($id, $request_arr);

                $type = 'success';
                $message = 'Inward Entry deleted successfully.';

                // delete records from order_items table
                if ($this->orderitem_obj->destroy($id, 'customer_order_id')) {
                    $type = 'success';
                    $message = 'Inward Entry deleted successfully.';
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
        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.INWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date','desc')->orderBy('sr_no','desc')->get();

        return view('admin.inwards.print')->with(compact('customer_orders','request','customers','total_additional_charge'));
    }
}
