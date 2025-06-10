<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerOrdersRequest;
use App\Models\Customer;
use App\Models\Marka;
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
            'breadcrumbs' => ['Home' => route('user.home'), 'Inwards' => ''],
            'menuParent' => 'inwards',
            'menuChild' => 'allinward',
        ];
        $user = Auth::User();

        $request['customer_id'] = $user->customer_id;
        $show = $request->filled('p') ? $request->input('p') : 100;

        $customers = Customer::where('customer_id', $user->customer_id)->withTrashed()->orderBy('companyname')->get();

        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.INWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->orderBy('customer_order_id', 'desc')->Paginate($show);

        return view('web.inwards.index', $data)->with(compact('customer_orders', 'request', 'customers', 'show', 'total_additional_charge'));
    }

    /**
     * Print list of Inwards.
     */
    public function print(Request $request)
    {
        $user = Auth::User();

        $request['customer_id'] = $user->customer_id;

        $customers = Customer::where('customer_id', $user->customer_id)->withTrashed()->orderBy('companyname')->get();
        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.INWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->get();

        return view('web.inwards.print')->with(compact('customer_orders', 'request', 'customers', 'total_additional_charge'));
    }

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
        $customer_order = $this->customerorder_obj->getCustomerOrder(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), $id)->first();
        $order_items = $this->orderitem_obj->getOrderItems($id, config('constant.CUSTOMER_ORDER_TYPE.INWARD'), $request)->get();

        if (empty($customer_order)) {
            abort(403);
        }

        return view('web.inwards.show-receipt', $data)->with(compact('customer_order', 'order_items', 'user', 'company_info'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerOrdersRequest $request)
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
     * Display Receipt for inward entries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Marka $obj_marka, $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
    }
}
