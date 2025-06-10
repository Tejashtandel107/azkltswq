<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Marka;
use App\Services\ImportService;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class MasterImportController extends Controller
{
    public function __construct(ImportService $obj_importservices)
    {
        $this->importservices_obj = $obj_importservices;
    }

    public function index(Request $request)
    {
        error_reporting(0);
        set_time_limit(0);

        $temp_dir = config('constant.TEMP_FOLDER_PATH');
        $now = Carbon::now();

        if (Auth::guest()) {
            exit('Please Login');
        }
        // Product model object

        //        list($data_arrs,$key_arr) = $this->importservices_obj->WriteImportCsv('temp/LOGBOOOK-06-04-2019.csv');

        foreach ($data_arrs as $key => $insert_data_arr) {
            $insert_data_arr_with_key[$key] = array_combine($key_arr, $insert_data_arr);
        }

        $counter = 0;

        foreach ($insert_data_arr_with_key as $key => $value) {
            $counter++;
            $input_data = [];

            $is_empty = $this->importservices_obj->isEmptyValue($value['Patry Name']);

            if (! $is_empty) {
                $customer = $this->getCustomerId($value['Patry Name']);
                if (empty($customer)) {
                    $stdClass = new \stdClass;
                    $stdClass->companyname = $value['Patry Name'];

                    Customer::create($this->addCustomer($stdClass));
                    $stdClass = null;
                }
            }

            $is_empty = $this->importservices_obj->isEmptyValue($value['Item Detail']);

            if (! $is_empty) {
                $item = $this->getItemId($value['Item Detail']);

                $insert_marka = false;

                if (empty($item)) {
                    $stdClass = new \stdClass;
                    $stdClass->name = $value['Item Detail'];
                    $item = Item::create($this->addItem($stdClass));
                    $stdClass = null;
                    $insert_marka = true;
                } else {
                    $marka = $this->getMarkaId($item->item_id, $value['Marka']);
                    if (empty($marka)) {
                        $insert_marka = true;
                    }
                }

                if ($insert_marka) {
                    $stdClass = new \stdClass;
                    $stdClass->name = $value['Marka'];
                    $stdClass->item_id = $item->item_id;

                    Marka::create($this->addMarka($stdClass));
                    $stdClass = null;
                }
            }
        }
    }

    public function getCustomerId($value)
    {
        return DB::table('customers')->select('customer_id', 'companyname')->where('companyname', $value)->first();
    }

    public function getItemId($value)
    {
        return Item::where('name', $value)->first();
    }

    public function getMarkaId($item_id, $value)
    {
        return Marka::where('item_id', $item_id)->where('name', $value)->first();
    }

    public function addCustomer($customer)
    {
        return ['companyname' => $customer->companyname];
    }

    public function addItem($item)
    {
        return ['name' => $item->name];
    }

    public function addMarka($marka)
    {
        return ['item_id' => $marka->item_id,
            'name' => $marka->name,
        ];
    }
}
