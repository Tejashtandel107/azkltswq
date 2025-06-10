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
use App\Services\OrderItemService;
use Auth;
use Carbon\Carbon;
use DB;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    protected $importservices_obj;

    public function __construct(ImportService $obj_importservices)
    {
        $this->importservices_obj = $obj_importservices;
    }

    public function index()
    {

        error_reporting(0);
        set_time_limit(0);

        $temp_dir = config('constant.TEMP_FOLDER_PATH');

        if (Auth::guest()) {
            exit('Please Login');
        }

        /* change the filename. Without any extension. */
        $filename = 'items';
        if (! empty($filename)) {
            [$data_arrs, $key_arr] = $this->importservices_obj->WriteImportCsv('temp/'.$filename.'.csv');
        } else {
            return 'No file given.';
        }

        foreach ($data_arrs as $key => $insert_data_arr) {
            $insert_data_arr_with_key[$key] = array_combine($key_arr, $insert_data_arr);
        }

        foreach ($insert_data_arr_with_key as $key => $value) {
            // $order_items = OrderItems::where('item_id', $value['item_id'])->where('type','inward')->get();
            // $price = number_format($value['price'], 2, '.', '');
            // OrderItems::where('item_id', $value['item_id'])->where('type','inward')->update(['item_rate' => $value['price']]);
        }

    }
    // public function index() {

    //  error_reporting(0);
    // set_time_limit(0);

    /*$results = OrderItems::select('order_items.*','i.name as item_name','m.name as marka_name')
            ->leftJoin('items as i', 'i.item_id', '=', 'order_items.item_id')
            ->leftJoin('marka as m', 'm.marka_id', '=', 'order_items.marka_id')
            ->get();

    //$customer_csv_arrays[] = ['Order Number','Date','Company Name','Type','sr No','Item Name','marka_name','vakkal_number','Weight','quantity','Chamber Number','Floor Number','Grid Number'];
    foreach ($results as $key => $value) {

        $isvalid_marka = Marka::select('marka_id','name')->where('marka_id',$value->marka_id)->where('item_id',$value->item_id)->count();
        if($isvalid_marka <= 0){
            $marka = Marka::select('marka_id','name')->where('name',$value->marka_name)->where('item_id',$value->item_id)->first();
            OrderItems::where('order_item_id', $value->order_item_id)->update(['marka_id' => $marka->marka_id]);
        }
    }*/
    /* $query_instances = OrderItems::select('order_items.*','i.name as item_name','m.name as marka_name','f.number as floor_number','g.number as grid_number','c.number as chamber_number','co.*','ca.*')
             ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
             ->leftJoin('customers as ca', 'ca.customer_id', '=', 'co.customer_id')
             ->leftJoin('items as i', 'i.item_id', '=', 'order_items.item_id')
             ->leftJoin('floor as f', 'f.floor_id', '=', 'order_items.floor_id')
             ->leftJoin('grid as g', 'g.grid_id', '=', 'order_items.grid_id')
             ->leftJoin('chamber as c', 'c.chamber_id', '=', 'order_items.chamber_id')
             ->leftJoin('marka as m', 'm.marka_id', '=', 'order_items.marka_id')
             ->get();

     $customer_csv_arrays[] = ['Order Number','Date','Company Name','Type','sr No','Item Name','marka_name','vakkal_number','Weight','quantity','Chamber Number','Floor Number','Grid Number'];
     foreach ($query_instances as $key => $value) {
         $check_marka = $this->getMarkaId($value->marka_id,$value->item_id);
         if(empty($check_marka)){
              $customer_csv_arrays[] = [$value->customer_order_id,$value->date,$value->companyname,$value->type,$value->sr_no,$value->item_name,$value->marka_name,$value->vakkal_number,$value->weight,$value->quantity,$value->chamber_number,$value->floor_number,$value->grid_number];
         }
     }

     if(count($customer_csv_arrays) > 0){
         $filename = $this->getStorageRealPath('temp/wrong_marka1.csv');
         $fp = fopen($filename, 'w');
         foreach ($customer_csv_arrays as $key => $customer_csv_value) {
             fputcsv($fp, $customer_csv_value);
         }
         fclose($fp);
     }*/
    // dump($query_instance);

    // $customer_order_id = [556,562,920];

    /*
    $results = OrderItems::select('*')->get();
    $results = $results->groupBy('vakkal_number');

    foreach($results as $vakkal_number=>$result){
        $counter = 1;
        $customer_order_ids = $result->pluck('customer_order_id')->toArray();

        $items = OrderItems::select("order_items.order_item_id")
                ->leftJoin("customer_orders as co","co.customer_order_id",'=',"order_items.customer_order_id")
                ->whereIn('order_items.customer_order_id',$customer_order_ids)
                ->where('order_items.vakkal_number',$vakkal_number)
                ->orderBy("co.date")
                ->orderBy( DB::raw("IF(order_items.type='inward',0,1)") )
                ->orderBy('order_items.order_item_id')
                ->get();

        foreach($items as $item) {
            $order_item_id = $item->order_item_id;

            $query3 = OrderItems::where('order_item_id',$order_item_id)->update(['sort' => $counter]);
            $counter++;
        }
    }

    exit(0);
    */
    // }
    public static function getMarkaId($marka_id, $item_id)
    {
        // if(Storage::exists($value)){
        return Marka::select('marka_id', 'name')->where('marka_id', $marka_id)->where('item_id', $item_id)->first();

    }

    public static function getStorageRealPath($file = '')
    {
        return Storage::disk(config('filesystems.local'))->path($file);
    }

    /*
        public function __construct(CustomerOrderService $customerorder,OrderItemService $orderitem)
        {
            $this->customerorder_obj = $customerorder;
            $this->orderitem_obj = $orderitem;
        }

        public function index(Request $request)
        {
            error_reporting(0);
            set_time_limit(0);

            $temp_dir = config ('constant.TEMP_FOLDER_PATH');
            $now = Carbon::now();

            if(Auth::guest()){
                die("Please Login");
            }
            //Product model object
            $temp_path = $this->getStoragePath('temp/inwards_outwards_csv_a.csv');
            // $temp_path = $this->getStoragePath('temp/log_book_for_jitesh.csv');
            dump($temp_path);
            //if(Storage::exists($temp_path)) {
                $data_arr[] = array();
                $counter = 0;

                $row = 1;
                if (($handle = fopen($temp_path, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                        $num = count($data);
                        for ($c=0; $c < $num; $c++) {
                            if($row == 1){
                                $key_arr[] = $data[$c];
                            }
                            else {
                                $data_arr[$counter][$c] = $data[$c];
                            }
                        }
                        if($row != 1){
                            $counter++;
                        }
                        $row++;
                    }
                    fclose($handle);
                }

                foreach ($data_arr as $key=>$insert_data_arr){
                    $insert_data_arr_with_key[$key] = array_combine($key_arr, $insert_data_arr);
                }

                /*
                foreach ($insert_data_arr_with_key as $key=>$value) {
                    $value = Helper::trimInputs($value);
                    $input_data = array ();
                    $counter = 0;
                    foreach ($value as $insert_data_arr_with_key_value_key => $insert_data_arr_with_key_value_value){
                        if ($insert_data_arr_with_key_value_key == 'Patry Name') {
                            if($insert_data_arr_with_key_value_value != ''){
                                //dump($insert_data_arr_with_key_value_value);
                                $Customers_info = $this->getCustomer($insert_data_arr_with_key_value_value);
                                if($Customers_info){
                                    $input_data['customer_id'] = $Customers_info->customer_id;
                                }
                                else{
                                    $item_id = Customer::insert(['companyname' => $insert_data_arr_with_key_value_value]);
                                    if($item_id){
                                        dump("done");
                                    }
                                }

                            }
                        }
                     }
                }

    */

    /*
                foreach ($insert_data_arr_with_key as $key=>$value) {
                    $value = Helper::trimInputs($value);
                    $input_data = array ();
                    $counter = 0;
                    foreach ($value as $insert_data_arr_with_key_value_key => $insert_data_arr_with_key_value_value){
                        if ($insert_data_arr_with_key_value_key == 'Date') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $input_data['date'] = $insert_data_arr_with_key_value_value;
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Patry Name') {
                            if($insert_data_arr_with_key_value_value != ''){
                                //dump($insert_data_arr_with_key_value_value);
                                $Customers_info = $this->getCustomer($insert_data_arr_with_key_value_value);
                                if($Customers_info){

                                }
                                else{

                                }
                                $input_data['customer_id'] = $Customers_info->customer_id;
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Inward no.') {
                            if($insert_data_arr_with_key_value_value != '' AND $insert_data_arr_with_key_value_value != '-'){
                                $input_data['sr_no'] = $insert_data_arr_with_key_value_value;
                                $input_data['type'] = 'inward';
                            }
                        }
                        if($insert_data_arr_with_key_value_key == 'Outward No.'){
                            if($insert_data_arr_with_key_value_value != '' AND $insert_data_arr_with_key_value_value != '-'){
                                $input_data['sr_no'] = $insert_data_arr_with_key_value_value;
                                $input_data['type'] = 'outward';
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Item Detail') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $items_info = $this->getItemId($insert_data_arr_with_key_value_value);
                                if($items_info){
                                    $input_data['item_id'] = [$items_info->item_id];
                                    $input_data['items_id'] = $items_info->item_id;
                                }
                                else{
                                    $item_id = Item::insertGetId(['name' => $insert_data_arr_with_key_value_value]);
                                    $input_data['item_id'] = [$item_id];
                                    $input_data['items_id'] = $item_id;
                                }
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Marka') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $marka_info = $this->getMarkaId($input_data['item_id'],$insert_data_arr_with_key_value_value);
                                if($marka_info){
                                    $input_data['marka_id'] = [$marka_info->marka_id];
                                }
                                else{
                                    $id = Marka::insertGetId(['item_id' => $input_data['items_id'], 'name' => $insert_data_arr_with_key_value_value]);
                                    dump($id);
                                    $input_data['marka_id'] = [$id];
                                }

                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Vakkal No.') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $input_data['vakkal_number'] = [$insert_data_arr_with_key_value_value];
                            }
                        }
                        if($input_data['type']=='outward') {
                            if ($insert_data_arr_with_key_value_key == 'Outwards qty') {
                                $input_data['quantity'] = [intval($insert_data_arr_with_key_value_value)];
                            }
                        }else {
                            if ($insert_data_arr_with_key_value_key == ' inwards qty') {
                                $input_data['quantity'] = [intval($insert_data_arr_with_key_value_value)];
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'package weight') {
                            if($insert_data_arr_with_key_value_value != '' && $insert_data_arr_with_key_value_value > 0){
                                $input_data['weight'] = [$insert_data_arr_with_key_value_value];
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'Chamber') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $chamber_info = $this->getChamberId($insert_data_arr_with_key_value_value);
                                $input_data['chamber_id'] = [$chamber_info->chamber_id];
                            }
                        }
                        if ($insert_data_arr_with_key_value_key == 'floor no.') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $floor_info = $this->getFloorId($insert_data_arr_with_key_value_value);
                                if($floor_info){
                                    $floor_no = $floor_info->floor_id;
                                }
                                else{
                                    $floor_no = 0;
                                }
                                $input_data['floor_id'] = [$floor_no];
                            }

                        }
                        if ($insert_data_arr_with_key_value_key == 'Grid NO.') {
                            if($insert_data_arr_with_key_value_value != ''){
                                $grid_info = $this->getGridId($insert_data_arr_with_key_value_value);
                                $input_data['grid_id'] = [$grid_info->grid_id];
                            }
                        }

                    }
                    if($input_data['customer_id']!=''){
                        dump($input_data);
                        $obj_order_items = new OrderItems();
                       $customerorder_data = $this->checkCustomerOrderExist($input_data['date'],$input_data['customer_id'],$input_data['sr_no'],$input_data['type']);
                       if(isset($customerorder_data) && $customerorder_data->count() > 0){
                            if($customerorder_data)
                            {
                                //store customer orders into DB
                                if($this->manageOrderItem($customerorder_data->customer_order_id,$input_data['type'],$input_data))
                                {
                                    dump("success 1");
                                }
                            }
                       }
                       else{
                             $result_customer_order = $this->customerorder_obj->store($input_data);
                            dump($result_customer_order['customer_order_id']);
                            if($result_customer_order)
                            {
                                //store customer orders into DB
                                if($this->manageOrderItem($result_customer_order['customer_order_id'],$input_data['type'],$input_data))
                                {
                                    dump("success");
                                }
                            }
                       }
                    }
                    else{
                        $filename = $this->getStorageRealPath('temp/Wrong_customer_entry_inwards_outwards_csv_5.csv');
                        $fp = fopen($filename, 'w');
                        //$temp_arrays = [$value['Date'],$value['Patry Name'],$value['Inward no.'],$value['Outward No.'],$value['Item Detail'],$value['Marka'],$value['Vakkal No.'],$value['package weight'],$value[' inwards qty'],$value['Inward Weight'],$value['Outwards qty'],$value['Outward Weight'],$value['Nos.'],$value['Weight'],$value['Chamber'],$value['floor no.'],$value['Grid NO.']];
                        $customer_csv_arrays[] = [$value['Date'],$value['Patry Name'],$value['Inward no.'],$value['Outward No.'],$value['Item Detail'],$value['Marka'],$value['Vakkal No.'],$value['package weight'],$value[' inwards qty'],$value['Inward Weight'],$value['Outwards qty'],$value['Outward Weight'],$value['Nos.'],$value['Weight'],$value['Chamber'],$value['floor no.'],$value['Grid NO.']];
                    }
                }

                dump(count($customer_csv_arrays));
                if (count($customer_csv_arrays) > 0) {
                    $i = 0;
                    foreach ($customer_csv_arrays as $key => $customer_csv_value) {
                        dump($i++);
                        dump("here1");
                        fputcsv($fp, $customer_csv_value);
                    }

                }
                fclose($f);

        }
        public static function getStoragePath($value){
            //if(Storage::exists($value)){
            if(strlen($value)>0){
                return Storage::url($value);
            }
            else{
                return '';
            }
        }
        public static function getCustomer($value){
            //if(Storage::exists($value)){
            return  Customer::select('customer_id','companyname')->where('companyname',$value)->first();
        }
        public static function getItemId($value){
            //if(Storage::exists($value)){
            return  Item::select('item_id','name')->where('name',$value)->first();
        }
        public static function getChamberId($value){
            //if(Storage::exists($value)){
            return  Chamber::select('chamber_id','number')->where('number',$value)->first();
        }
        public static function getFloorId($value){
            //if(Storage::exists($value)){
            return  Floor::select('floor_id','number')->where('number',$value)->first();
        }
        public static function getGridId($value){
            //if(Storage::exists($value)){
            return  Grid::select('grid_id','number')->where('number',$value)->first();
        }
        public static function getMarkaId($id,$value){
            //if(Storage::exists($value)){
            if($id > 0){
                return  Marka::select('marka_id','name')->where('name',$value)->where('item_id',$id)->first();
            }
            return 0;

        }
        public function checkCustomerOrderExist($date,$customer_id,$sr_no,$type){
            return CustomerOrders::select('customer_order_id')->whereDate('date',Helper::convertDateFormat($date))->where('customer_id',$customer_id)->where('sr_no',$sr_no)->where('type',$type)->first();
        }
        public static function getStorageRealPath($file=""){
            return Storage::disk(config('filesystems.local'))->getDriver()->getAdapter()->applyPathPrefix($file);
        }
        public function manageOrderItem($customer_order_id,$order_type,$request_arr)
        {
            $item_id = !empty($request_arr['item_id']) ? $request_arr['item_id'] : null ;
            if (count($item_id)>0)
            {
                $now = Carbon::now();
                $items_arr = array();
                $marka_id = $request_arr['marka_id'];
                $vakkal_number = $request_arr['vakkal_number'];
                $chamber_id = $request_arr['chamber_id'];
                $floor_id = $request_arr['floor_id'];
                $grid_id = $request_arr['grid_id'];
                $weight = $request_arr['weight'];
                $bag_quantity = $request_arr['quantity'];
                $created_at = $request_arr['created_at'] ?? $now;


                //days and rate for outward entries.
                $days = isset($request_arr['no_of_days']) ? $request_arr['no_of_days'] : null ;
                $rate = isset($request_arr['rate']) ? $request_arr['rate'] : null ;


                for ($i = 0; $i < count($item_id); $i++)
                {
                    if ($item_id[ $i ]!="" && $vakkal_number[ $i ] != "")
                    {
                        $items_arr[] = array(
                            'customer_order_id' => $customer_order_id,
                            'type' => $order_type,
                            'item_id' => $item_id[$i],
                            'marka_id' => $marka_id[$i],
                            'vakkal_number' => $vakkal_number[$i],
                            'chamber_id'=> $chamber_id[$i],
                            'floor_id'=> $floor_id[$i],
                            'grid_id'=> $grid_id[$i],
                            'weight' => $weight[$i],
                            'quantity' => $bag_quantity[$i],
                            'no_of_days' => $days[$i],
                            'rate' => $rate[$i],
                            'created_at' => $created_at,
                            'updated_at' => $now
                        );
                    }
                }
                if(count($items_arr)>0)
                {
                    $result = OrderItems::insert($items_arr);
                    if($result)
                    {
                        //clear memory
                        $customer_order_id = $order_type = $item_id = $vakkal_number =  $weight = $bag_quantity = $marka_id = $chamber_id = $grid_id = $floor_id = $items_arr = null;
                        return true;
                    }
                }
            }
            return false;
        }
    */

}
