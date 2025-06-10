<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marka;
use Illuminate\Http\Request;

class MarkaController extends Controller
{
    /**
     * Fetch all marka by item_id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function fetchMarka(Request $request)
    {
        $item_id = $request->filled('id') ? $request->id : 0;
        $markas = Marka::withTrashed()->select('name', 'marka_id')->where('item_id', $item_id)->orderBy('name', 'asc')->get()->toArray();

        if (isset($markas)) {
            return response()->json($markas);
        } else {
            return response()->json(['status' => 404]);
        }
    }

    public function fetchCustomerMarka(Request $request)
    {

        $item_id = $request->filled('id') ? $request->id : 0;
        $customer_id = $request->filled('customer_id') ? $request->customer_id : 0;

        $markas = Marka::select('name', 'marka.marka_id')->leftjoin('order_items as oi', 'oi.marka_id', '=', 'marka.marka_id')
            ->leftjoin('customer_orders as co', 'co.customer_order_id', '=', 'oi.customer_order_id')
            ->withTrashed()->where('co.customer_id', $customer_id)
            ->where('marka.item_id', $item_id)->orderBy('name', 'asc')->groupBy('oi.marka_id')->get()->toArray();

        // $markas = Marka::withTrashed()->select('name','marka_id')->where('item_id',$item_id)->orderBy('name','asc')->get()->toArray();

        if (isset($markas)) {
            return response()->json($markas);
        } else {
            return response()->json(['status' => 404]);
        }
    }
}
