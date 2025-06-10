<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerOrdersRequest;
use App\Models\Customer;
use App\Models\Marka;
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
            'breadcrumbs' => ['Home' => route('user.home'), 'Outwards' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'alloutward',
        ];
        $user = Auth::User();
        $request['customer_id'] = $user->customer_id;

        $show = $request->filled('p') ? $request->input('p') : 100;

        $customers = Customer::where('customer_id', $user->customer_id)->withTrashed()->orderBy('companyname')->get();

        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->orderBy('customer_order_id', 'desc')->Paginate($show);

        return view('web.outwards.index', $data)->with(compact('customer_orders', 'request', 'customers', 'show', 'total_additional_charge'));
    }

    /**
     * Print list of Inwards.
     */
    public function print(Request $request)
    {
        $user = Auth::User();

        $customers = Customer::where('customer_id', $user->customer_id)->withTrashed()->orderBy('companyname')->get();
        $request['customer_id'] = $user->customer_id;

        $queryInstance = $this->customerorder_obj->filterListByType($request, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'));
        $total_additional_charge = $queryInstance->sum('additional_charge');
        $customer_orders = $queryInstance->orderBy('customer_orders.date', 'desc')->orderBy('sr_no', 'desc')->get();

        return view('web.outwards.print')->with(compact('customer_orders', 'request', 'customers', 'total_additional_charge'));
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
     * Display Receipt for outward entries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showReceipt(Request $request, $id)
    {
        $data = [
            'pagetitle' => 'Outward Receipt',
            'breadcrumbs' => ['Home' => route('user.home'), 'Outwards' => route('user.outwards.index'), 'Receipt' => ''],
            'menuParent' => 'outwards',
            'menuChild' => 'alloutward',
        ];
        $user = Auth::User();

        $company_info = $this->customerorder_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));

        $customer_order = $this->customerorder_obj->getCustomerOrder(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), $id)->first();
        $queryInstance = $this->orderitem_obj->getOrderItems($id, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), $request);

        $queryInstance->addSelect(DB::raw('(SELECT sum(if(oi.type=?,oi.quantity,0))-sum(if(oi.type=?,oi.quantity,0)) FROM order_items as oi WHERE oi.vakkal_number = order_items.vakkal_number and oi.item_id=order_items.item_id and oi.sort <= order_items.sort) AS balance_quantity'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select')->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');

        $outward_items = $queryInstance->get();

        if (empty($customer_order)) {
            abort(403);
        }

        return view('web.outwards.show-receipt', $data)->with(compact('customer_order', 'outward_items', 'user', 'company_info'));
    }

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerOrdersRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        //
    }
}
