<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerOrders;
use App\Services\CustomerOrderService;
use App\Services\OrderItemService;
use Helper;
use Illuminate\Http\Request;

class OrderTrashController extends Controller
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
            'pagetitle' => 'All Trash Orders',
            'breadcrumbs' => ['Home' => route('admin.home'), 'All Trash Orders' => ''],
            'menuParent' => 'trash',
            'menuChild' => 'trashorders',
        ];
        $show = $request->filled('p') ? $request->input('p') : 100;
        $customers = Customer::withTrashed()->orderBy('companyname')->get();
        $queryInstance = CustomerOrders::select('u.firstname', 'u.lastname', 'customer_orders.*', 'c.*', 'c.address as customer_add', 'customer_orders.address as delivery_address')
            ->leftJoin('customers as c', 'c.customer_id', '=', 'customer_orders.customer_id')
            ->leftJoin('users as u', 'u.user_id', '=', 'customer_orders.deleted_user_id');

        if ($request->filled('from')) {
            $from = Helper::convertDateFormat($request->input('from'));
            $queryInstance->whereDate('customer_orders.date', '>=', $from);
        }
        if ($request->filled('to')) {
            $to = Helper::convertDateFormat($request->input('to'));
            $queryInstance->whereDate('customer_orders.date', '<=', $to);
        }

        if ($request->filled('customer_id')) {
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

        $total_additional_charge = $queryInstance->onlyTrashed()->sum('additional_charge');
        $customer_orders = $queryInstance->onlyTrashed()->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->Paginate($show);
        // print_r($customer_orders->toArray());
        // exit(0);

        return view('admin.trash.order', $data)->with(compact('customer_orders', 'request', 'customers', 'show', 'total_additional_charge'));
    }

    public function restoreOrder(Request $request, $id)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to restored Orders Entry. Please try again.';

            // delete records from customer_orders table
            if ($this->customerorder_obj->restoreOrder($id)) {
                $type = 'success';
                $message = 'Order restored successfully.';

                // delete records from order_items table
                if ($this->orderitem_obj->restoreOrder($id, 'customer_order_id')) {
                    $type = 'success';
                    $message = 'Order restored successfully.';
                }

                $request_arr['deleted_user_id'] = 0;
                $this->customerorder_obj->update($id, $request_arr);
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
            $message = 'Sorry, failed to delete Order Entry. Please try again.';

            // delete records from customer_orders table
            if ($this->customerorder_obj->forceDestroy($id)) {
                $type = 'success';
                $message = 'Order Entry deleted successfully.';

                // delete records from order_items table
                if ($this->orderitem_obj->forceDestroy($id, 'customer_order_id')) {
                    $type = 'success';
                    $message = 'Order Entry deleted successfully.';
                }
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }
}
