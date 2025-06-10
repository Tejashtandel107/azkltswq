<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Marka;
use App\Services\CustomerOrderService;
use Auth;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;

class InsuranceReportsController extends Controller
{
    protected $customerorder_obj;

    public function __construct(CustomerOrderService $customerorder)
    {
        $this->customerorder_obj = $customerorder;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(Request $request)
    {
        $user = Auth::user();
        $data = [
            'pagetitle' => 'Insurance Report',
            'breadcrumbs' => ['Home' => route('user.home'), 'Statement Of Insurance' => ''],
            'menuParent' => 'reports',
            'menuChild' => 'insuarnce-report',
        ];

        $period = '';

        $user = Auth::user();

        // $customers = Customer::where('customer_id',$user->customer_id)->withTrashed()->orderBy('companyname')->get();
        $request['c'] = $user->customer_id;

        if ($request->filled('c') && ($request->filled('from') || $request->filled('to'))) {
            $customer_id = $request->input('c');
            $from = Carbon::parse($request->input('from').'-01');
            $to = Carbon::parse($request->input('to').'-01');

            if ($request->filled('from') && $request->filled('to') && $from->toDateString() > $to->toDateString()) {
                $from = Carbon::parse($request->input('to').'-01');
                $to = Carbon::parse($request->input('from').'-01');
            }
            if (! $request->filled('from')) {
                $from = Carbon::parse($request->input('to').'-01');
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
                ->select(DB::raw('YEAR(mt.merge_date) AS Year'), DB::raw("DATE_FORMAT(merge_date,'%b') AS Month"), 'co.date', 'i.name as item_name', 'm.name as marka_name', 'c.companyname', 'c.address', DB::raw('max(oi.item_rate) as item_rate'), DB::raw('max(oi.insurance_rate) as insurance_rate'), 'oi.vakkal_number', 'oi.weight')
                ->leftJoin('customer_orders as co', 'co.date', '<=', 'mt.merge_date')
                ->leftJoin('order_items as oi', 'oi.customer_order_id', '=', 'co.customer_order_id')
                ->leftJoin('customers as c', 'c.customer_id', '=', 'co.customer_id')
                ->leftJoin('items as i', 'i.item_id', '=', 'oi.item_id')
                ->leftJoin('marka as m', 'm.marka_id', '=', 'oi.marka_id')
                ->where('co.customer_id', $customer_id);

            $queryInstance->addSelect(DB::raw('SUM(if(oi.type=?,oi.quantity,0)) as inwards'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.INWARD'), 'select');
            $queryInstance->addSelect(DB::raw('SUM(if(date_add(merge_date,interval - DAY(merge_date)+3 DAY) >= co.`date` and oi.type=?,oi.quantity,0)) as outwards'))->addBinding(config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'), 'select');
            $queryInstance->addSelect(DB::raw('SUM(IF(oi.type=?,oi.quantity*oi.weight,0))-SUM(IF(date_add(merge_date,interval -DAY(merge_date)+1 DAY) > co.`date` and oi.type=?,oi.quantity*oi.weight,0)) as total_balance_weight'))->addBinding([config('constant.CUSTOMER_ORDER_TYPE.INWARD'), config('constant.CUSTOMER_ORDER_TYPE.OUTWARD')], 'select');

            $queryInstance->having(DB::raw('inwards - outwards'), '>', 0)
                ->groupBy('mt.merge_date')
                ->groupBy('oi.vakkal_number');

            if ($request->filled('i')) {
                $item_id = $request->input('i');
                $queryInstance->where('oi.item_id', $item_id);
            }

            if ($request->filled('m')) {
                $marka_id = $request->input('m');
                $queryInstance->where('oi.marka_id', $marka_id);
            }

            $keyword = $request->input('q');

            if ($keyword != '') {
                $search_fields = ['i.name', 'm.name', 'oi.vakkal_number'];
                $queryInstance->Where(function ($query) use ($keyword, $search_fields) {
                    $words = explode(' ', $keyword);
                    foreach ($search_fields as $field) {
                        $query->orWhere(function ($query) use ($words, $field) {
                            foreach ($words as $word) {
                                return $query->Where($field, 'like', '%'.$word.'%');
                            }
                        });
                    }
                });
            }

            $results = $queryInstance->whereNull('co.deleted_at')->whereNull('oi.deleted_at')->orderBy('i.name')->orderBy('m.name')->orderBy('vakkal_number')->orderBy('mt.merge_date')->get();
        } else {
            $results = collect();
        }

        $customer_id = isset($customer_id) ? $customer_id : $user->customer_id;

        if ($request->filled('c')) {
            $items = $this->customerorder_obj->fetchCustomerItems($customer_id);

            // $items = Item::withTrashed()->orderBy('name')->get();
        }

        if ($request->filled('i') && $request->filled('c')) {
            $markas = $this->customerorder_obj->fetchCustomerMarka($request->input('i'), $customer_id);
            // $markas = Marka::withTrashed()->where('item_id',$request->input('i'))->orderBy('name')->get();
        } else {
            $markas = collect();
        }

        $company_info = $this->customerorder_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));

        return view('web.reports.insurance', $data)->with(compact('user', 'request', 'items', 'markas', 'results', 'period', 'company_info', 'customer_id'));
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
    public function destroy($id)
    {
        //
    }
}
