<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chamber;
use App\Models\Customer;
use App\Models\CustomerOrders;
use App\Models\Floor;
use App\Models\Grid;
use App\Models\Item;
use App\Models\Marka;
use App\Models\OrderItems;
use App\Services\CustomerOrderService;
use App\Services\ImportService;
use Auth;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;

class OrderItemImportController extends Controller
{
    public function __construct(CustomerOrderService $customerorder, ImportService $obj_importservices)
    {
        $this->customerorder_obj = $customerorder;
        $this->importservices_obj = $obj_importservices;
    }

    public function index(Request $request)
    {
        error_reporting(0);
        set_time_limit(0);

        $now = Carbon::now();

        if (Auth::guest()) {
            exit('Please Login');
        }
        // Product model object
        [$data_arr, $key_arr] = $this->importservices_obj->WriteImportCsv('temp/LOGBOOOK-06-04-2019.csv');

        foreach ($data_arr as $key => $insert_data_arr) {
            $insert_data_arr_with_key[$key] = array_combine($key_arr, $insert_data_arr);
        }
        $all_customers = Customer::all();
        $all_markas = Marka::all();
        $all_items = Item::all();
        $all_grids = Grid::all();
        $all_chambers = Chamber::all()->keyBy('number');
        $all_floors = Floor::all()->keyBy('number');
        foreach ($insert_data_arr_with_key as $key => $value) {
            // dd($value);
            $input_array = [];
            $is_empty = $this->importservices_obj->isEmptyValue($value['Date']);
            if (! $is_empty) {
                $input_array['date'] = $value['Date'];
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Patry Name']);
            if (! $is_empty) {
                $customers = $this->getCustomerId($all_customers, $value['Patry Name']);
                $input_array['customer_id'] = $customers->customer_id;
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue(intval($value['Inward no.']));
            if (! $is_empty) {
                $input_array['sr_no'] = intval($value['Inward no.']);
                $input_array['type'] = 'inward';
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue(intval($value['Outward No.']));
            if (! $is_empty) {
                $input_array['sr_no'] = intval($value['Outward No.']);
                $input_array['type'] = 'outward';
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Item Detail']);
            if (! $is_empty) {
                $items = $this->getItemId($all_items, $value['Item Detail']);
                $input_array['item_id'] = $items->item_id;
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Marka']);
            if (! $is_empty) {
                $marka = $this->getMarkaId($all_markas, $input_array['item_id'], $value['Marka']);
                $input_array['marka_id'] = $marka->marka_id;
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Vakkal No.']);
            if (! $is_empty) {
                $input_array['vakkal_number'] = $value['Vakkal No.'];
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['package weight']);
            if (! $is_empty) {
                $input_array['weight'] = $value['package weight'];
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Inward qty']);
            if (! $is_empty) {
                $input_array['quantity'] = $value['Inward qty'];
            }
            $is_empty = null;

            $is_empty = $this->importservices_obj->isEmptyValue($value['Outward qty']);
            if (! $is_empty) {
                $input_array['quantity'] = $value['Outward qty'];
            }
            $is_empty = null;

            // $is_empty = $this->importservices_obj->isEmptyValue($value['Chamber']);
            $chambers = $this->getChamberId($all_chambers, $value['Chamber']);
            if ($chambers) {
                $input_array['chamber_id'] = $chambers->chamber_id;
            }
            // $is_empty = null;

            $floors = $this->getFloorId($all_floors, $value['floor no.']);
            if ($floors) {
                $input_array['floor_id'] = $floors->floor_id;
            }

            $is_empty = $this->importservices_obj->isEmptyValue($value['Grid NO.']);
            if (! $is_empty) {
                $grids = $this->getGridId($all_grids, $value['Grid NO.']);
                $input_array['grid_id'] = $grids->grid_id;
            }
            $is_empty = null;

            if ($input_array['customer_id'] != '') {
                $customerorder_data = $this->checkCustomerOrderExist($input_array['date'], $input_array['customer_id'], $input_array['sr_no'], $input_array['type']);

                if (isset($customerorder_data) && $customerorder_data->count() > 0) {
                    // store into order_items table
                    $r[] = $this->manageOrderItem($customerorder_data->customer_order_id, $input_array['type'], $input_array);
                } else {
                    // store into customer_orders table
                    $result_customer_order = $this->customerorder_obj->store($input_array);
                    if ($result_customer_order) {
                        $r[] = $this->manageOrderItem($result_customer_order['customer_order_id'], $input_array['type'], $input_array);
                    }
                }
            } else {
                // $temp_arrays = [$value['Date'],$value['Patry Name'],$value['Inward no.'],$value['Outward No.'],$value['Item Detail'],$value['Marka'],$value['Vakkal No.'],$value['package weight'],$value[' inwards qty'],$value['Inward Weight'],$value['Outwards qty'],$value['Outward Weight'],$value['Nos.'],$value['Weight'],$value['Chamber'],$value['floor no.'],$value['Grid NO.']];
                $customer_csv_arrays[] = [$value['Date'], $value['Patry Name'], $value['Inward no.'], $value['Outward No.'], $value['Item Detail'], $value['Marka'], $value['Vakkal No.'], $value['package weight'], $value[' inwards qty'], $value['Inward Weight'], $value['Outwards qty'], $value['Outward Weight'], $value['Nos.'], $value['Weight'], $value['Chamber'], $value['floor no.'], $value['Grid NO.']];
            }

        }

        // $collection = collect($r);
        // $chunks = $collection->chunk(100);
        // $chunks->toArray();
        // foreach($chunks as $chunk) {
        //     if(OrderItems::insert($chunk->toArray())) {
        //         //dump($chunk->count());
        //         //dump("success");
        //     }
        // }
        foreach ($r as $value) {
            if (OrderItems::insert($value)) {
                // dump($chunk->count());
                // dump("success");
            }
        }

        // $r = $input_data = null;

    }

    public static function getCustomerId($all_customers, $value)
    {

        $filtered = $all_customers->filter(function ($item) use ($value) {
            $companyname = strtolower($item->companyname);

            return $companyname == strtolower($value);
        });

        return $filtered->first();
    }

    public static function getItemId($all_items, $value)
    {

        $filtered = $all_items->filter(function ($item) use ($value) {
            $itemname = strtolower($item->name);

            return $itemname == strtolower($value);
        });

        return $filtered->first();
    }

    public static function getMarkaId($all_markas, $item_id, $value)
    {

        $filtered = $all_markas->filter(function ($item) use ($value) {
            $markaname = strtolower($item->name);

            return $markaname == strtolower($value);
        });

        $filtered->filter(function ($item) use ($item_id) {
            return $item->item_id == $item_id;
        });

        return $filtered->first();
    }

    public static function getChamberId($all_chambers, $value)
    {
        // return Chamber::where('number', $value)->first();
        return $all_chambers->get($value);
    }

    public static function getFloorId($all_floors, $value)
    {
        return $all_floors->get($value);
    }

    public static function getGridId($all_grids, $value)
    {
        $value_arr = explode(',', $value);

        return Grid::where('number', trim($value_arr[0]))->first();
        // $filtered = $all_grids->filter(function($item) use($value)  {
        //     return $item->grid_id == $value[0];
        // });

        // return $filtered->first();
    }

    public function checkCustomerOrderExist($date, $customer_id, $sr_no, $type)
    {
        $date = Helper::convertDateFormat($date, '!m/d/Y');

        return CustomerOrders::select('customer_order_id')->whereDate('date', $date)->where('customer_id', $customer_id)->where('sr_no', $sr_no)->where('type', $type)->first();
    }

    public function manageOrderItem($customer_order_id, $order_type, $request_arr)
    {
        $item_id = ! empty($request_arr['item_id']) ? $request_arr['item_id'] : null;
        if (count($item_id) > 0) {
            $now = Carbon::now();
            $created_at = $request_arr['created_at'] ?? $now;
            // days and rate for outward entries.
            $days = isset($request_arr['no_of_days']) ? $request_arr['no_of_days'] : null;
            $rate = isset($request_arr['rate']) ? $request_arr['rate'] : null;

            return ['customer_order_id' => $customer_order_id,
                'type' => $order_type,
                'item_id' => $item_id,
                'marka_id' => $request_arr['marka_id'],
                'vakkal_number' => $request_arr['vakkal_number'],
                'chamber_id' => $request_arr['chamber_id'],
                'floor_id' => $request_arr['floor_id'],
                'grid_id' => $request_arr['grid_id'],
                'weight' => $request_arr['weight'],
                'quantity' => $request_arr['quantity'],
                'no_of_days' => $days,
                'rate' => $rate,
                'created_at' => $created_at,
                'updated_at' => $now];
        }
    }
}
