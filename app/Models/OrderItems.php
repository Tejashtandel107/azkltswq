<?php

namespace App\Models;

use App\Services\OrderItemService;
use Carbon\Carbon;
use DB;
use Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class OrderItems extends Model
{
    use SoftDeletes;

    protected $table = 'order_items';

    protected $primaryKey = 'order_item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_item_id', 'customer_order_id', 'type', 'item_id', 'marka_id', 'vakkal_number', 'chamber_id', 'floor_id', 'grid_id', 'item_rate', 'insurance_rate', 'cooling_charge_rate', 'weight', 'quantity', 'no_of_days', 'rate', 'description', 'is_taxable', 'sort'];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the Date.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function date(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::DateFormat($value, config('constant.DATE_FORMAT_SHORT')),
        );
    }

    /**
     * Get the Date.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function getDescriptionAttribute($value)
    {
        return (empty($value)) ? '-' : $value;
    }

    /**
     * Get total weight.
     *
     * @return int
     */
    public function totalWeight(): Attribute
    {
        return new Attribute(
            get: fn () => $this->weight * $this->quantity,
        );
    }

    /**
     * Get amount based on weight and days.
     *
     * @return int
     */
    public function totalAmount(): Attribute
    {
        return new Attribute(
            get: fn () => ($this->rate / 30) * $this->no_of_days * $this->weight * $this->quantity,
        );
    }

    /**
     * Get the Company name.
     *
     * @return {companyname}
     */
    public function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => $this->companyname,
        );
    }

    /**
     * check if order type is inward.
     */
    public function isInward()
    {
        return $this->type == config('constant.CUSTOMER_ORDER_TYPE.INWARD');
    }

    /**
     * check if order type is outward.
     */
    public function isOutward()
    {
        return $this->type == config('constant.CUSTOMER_ORDER_TYPE.OUTWARD');
    }

    /**
     * Scope a query to only include Inward items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeInward($query)
    {
        return $query->where('type', config('constant.CUSTOMER_ORDER_TYPE.INWARD'));
    }

    /**
     * Scope a query to only include Outward items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeOutward($query)
    {
        return $query->where('type', config('constant.CUSTOMER_ORDER_TYPE.OUTWARD'));
    }

    public function manageOrderItem($customer_order_id, $order_type, $request_arr)
    {
        $response = new \stdClass;
        $response->type = 'error';
        $item_id = ! empty($request_arr['item_id']) ? $request_arr['item_id'] : null;
        if (isset($item_id) && count($item_id) > 0) {
            $now = Carbon::now();
            $items_arr = [];
            $marka_id = $request_arr['marka_id'];
            $vakkal_number = $request_arr['vakkal_number'];
            $chamber_id = $request_arr['chamber_id'];
            $floor_id = $request_arr['floor_id'];
            $grid_id = $request_arr['grid_id'];
            $item_rate = isset($request_arr['item_rate']) ? $request_arr['item_rate'] : null;
            $insurance_rate = isset($request_arr['insurance_rate']) ? $request_arr['insurance_rate'] : null;
            $weight = $request_arr['weight'];
            $bag_quantity = $request_arr['quantity'];
            $created_at = $request_arr['created_at'] ?? $now;
            $deleted_at = $request_arr['deleted_at'] ?? null;
            // days and rate for outward entries.
            $days = isset($request_arr['no_of_days']) ? $request_arr['no_of_days'] : null;
            $rate = isset($request_arr['rate']) ? $request_arr['rate'] : null;
            $details = isset($request_arr['details']) ? $request_arr['details'] : null;
            $is_taxable = isset($request_arr['is_taxable']) ? $request_arr['is_taxable'] : 0;

            foreach ($item_id as $index => $item) {
                $record = Marka::withTrashed()->where('item_id', $item)->where('marka_id', $marka_id[$index])->count();
                if ($record == 0) {
                    $message[] = 'The selected item and marka pair for Vakkal number: '.$vakkal_number[$index].', Weight: '.$weight[$index].', Quantity: '.$bag_quantity[$index].' is invalid.';
                }
            }
            if (isset($message)) {
                $response->success = false;
                $response->message = implode('<br>', $message);

                return $response;
            }

            for ($i = 0; $i < count($item_id); $i++) {
                if ($item_id[$i] != '' && $vakkal_number[$i] != '') {
                    $items_arr[] = [
                        'customer_order_id' => $customer_order_id,
                        'type' => $order_type,
                        'item_id' => $item_id[$i],
                        'marka_id' => $marka_id[$i],
                        'vakkal_number' => $vakkal_number[$i],
                        'chamber_id' => $chamber_id[$i],
                        'floor_id' => $floor_id[$i],
                        'grid_id' => $grid_id[$i],
                        'item_rate' => ($item_rate[$i]) ?? null,
                        'insurance_rate' => ($insurance_rate[$i]) ?? null,
                        'weight' => $weight[$i],
                        'quantity' => $bag_quantity[$i],
                        'no_of_days' => ($days[$i]) ?? null,
                        'rate' => ($rate[$i]) ?? null,
                        'description' => ($details[$i]) ?? null,
                        'is_taxable' => ($is_taxable[$i]) ?? 0,
                        'created_at' => $created_at,
                        'updated_at' => $now,
                        'deleted_at' => $deleted_at,
                    ];
                }
            }
            if (count($items_arr) > 0) {
                $order_items = OrderItems::withTrashed()->where('customer_order_id', $customer_order_id);
                if ($order_items->count()) {
                    $order_items->forceDelete();
                }

                $result = OrderItems::insert($items_arr);
                if ($result) {
                    // clear memory
                    $customer_order_id = $order_type = $item_id = $vakkal_number = $weight = $bag_quantity = $marka_id = $chamber_id = $grid_id = $floor_id = $items_arr = null;
                    $response->success = true;
                    $response->type = 'success';

                    return $response;
                }
            }
        }

        $response->success = false;
        $response->message = 'Sorry, failed to update Outward Entry. Please try again.';

        return $response;
    }

    /**
     * Add Inward/Outward Customer Order into DB.
     *
     * @param  int  $customer_order_id
     * @param  array  $request_arr
     * @param  string  $order_type=inward/outward
     * @return bool
     */
    public function addOrderItems($customer_order_id, $request_arr, $order_type)
    {
        return $this->manageOrderItem($customer_order_id, $order_type, $request_arr);
    }

    /**
     * Update Inward/Outward Order Items into DB.
     *
     * @param  int  $customer_order_id
     * @param  array  $request_arr
     * @param  string  $order_type=inward/outward
     * @return bool
     */
    public function updateOrderItems($customer_order_id, $request_arr, $order_type)
    {
        // $order_items = OrderItems::where('customer_order_id',$customer_order_id);
        // if($order_items->count())
        // {
        //     $order_items->delete();
        // }
        return $this->manageOrderItem($customer_order_id, $order_type, $request_arr);
    }

    public function sortRecords($vakkal_numbers = [])
    {

        $results = OrderItems::withTrashed()->whereIn('vakkal_number', $vakkal_numbers)->get();
        $results = $results->groupBy('vakkal_number');

        foreach ($results as $vakkal_number => $result) {
            $customer_order_ids = $result->pluck('customer_order_id')->toArray();
            $counter = 1;

            $items = OrderItems::withTrashed()->select('order_items.order_item_id')
                ->leftJoin('customer_orders as co', 'co.customer_order_id', '=', 'order_items.customer_order_id')
                ->whereIn('order_items.customer_order_id', $customer_order_ids)
                ->where('order_items.vakkal_number', $vakkal_number)
                ->orderBy('co.date')
                ->orderBy(DB::raw("IF(order_items.type='inward',0,1)"))
                ->orderBy('order_items.order_item_id')
                ->get();

            foreach ($items as $item) {
                $order_item_id = $item->order_item_id;
                $query3 = OrderItems::withTrashed()->where('order_item_id', $order_item_id)->update(['sort' => $counter]);
                $counter++;
            }
        }

        return true;
    }

    public function getInsurancePayment(Request $request)
    {
        $order_Item_service = new OrderItemService($this);

        return $order_Item_service->getOrderInsurance($request, false);
    }
}
