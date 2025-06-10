<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderItemService;
use Auth;
use Illuminate\Http\Request;

class StockReportsController extends Controller
{
    protected $orderitem_obj;

    public function __construct(OrderItemService $orderitem)
    {
        $this->orderitem_obj = $orderitem;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

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
            'pagetitle' => 'Storage Capacity Report',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Storage Capacity' => ''],
            'menuParent' => 'reports',
            'menuChild' => 'stock-report',
        ];

        [, , $chambers, $floors, $grids] = $this->orderitem_obj->pluckData(['chamber_id', 'floor_id', 'grid_id']);
        $results = $this->orderitem_obj->manageStorageCapacity($request)->get();
        $company_info = $this->orderitem_obj->getCompanyDetails(config('constant.SETTINGS_KEY'));

        return view('admin.reports.storage-capacity', $data)->with(compact('user', 'results', 'request', 'chambers', 'floors', 'grids', 'company_info'));
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
