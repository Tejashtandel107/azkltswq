<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerOrderService;
use App\Services\OrderItemService;
use Auth;
use Helper;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $orderitem_obj;

    protected $customer_order_obj;

    public function __construct(OrderItemService $orderitem, CustomerOrderService $customer_order)
    {
        $this->orderitem_obj = $orderitem;
        $this->customer_order_obj = $customer_order;

    }

    public function index(Request $request)
    {
        $data = [
            'menuChild' => 'dashboard',
        ];

        // $customers = Customer::withTrashed()->orderBy('companyname')->get();

        return view('web.dashboard.index', $data)->with(compact('request'));

    }

    public function show(Request $request)
    {
        $user = Auth::User();
        $request->merge(['r' => ['new', 'final'], 'c' => $user->customer_id]);
        $stock_statistics = $this->orderitem_obj->calculateCurrentBalance($request);
        $calculate_stocks = $this->orderitem_obj->calculateInwardOutwardWeight($request);
        $additional_charge = $this->customer_order_obj->calculateAdditionalCharge($request);

        $outward_total_amount = $this->orderitem_obj->calculateStorageCharge(0, config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), $request)->get()->sum('total_amount');

        $insurance_statistics = $this->orderitem_obj->getOrderInsurance($request, false);

        return view('web.dashboard.includes.index')->with(compact('request', 'stock_statistics', 'additional_charge', 'outward_total_amount', 'insurance_statistics', 'calculate_stocks'));
    }

    /**
     * For AJAX Call: Display amount of RECEIPTS and PAYMENTS.
     *
     * @return \Illuminate\Http\Response
     */
    public function showstatistics(Request $request)
    {
        $from = $request->filled('from') ? $request->input('from') : '';
        $to = $request->filled('to') ? $request->input('to') : Helper::DateFormat(now(), config('constant.DATE_FORMAT_SHORT'));

        $obj_customer_income = new Income;
        $incomes = $obj_customer_income->getTotal()->whereBetween('paid_at', [$from, $to])->get();

        $obj_fishing_expense = new Payment;
        $payments = $obj_fishing_expense->getTotal()->whereBetween('paid_at', [$from, $to])->get();

        $obj_fishing_transaction = new FishingTransaction;
        $fishings_transaction = $obj_fishing_transaction->getTotalIncome()->whereBetween('fishing_date', [$from, $to])->get();
        $fishings_income = $obj_customer_income->getIncomeforFishing($from, $to);

        return view('admin.dashboard.statistics')->with(compact('incomes', 'payments', 'from', 'to', 'fishings_transaction', 'fishings_income'));
    }
}
