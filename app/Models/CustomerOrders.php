<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrders extends Model
{
    use SoftDeletes;

    protected $table = 'customer_orders';

    protected $primaryKey = 'customer_order_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['customer_id', 'user_id', 'type', 'date', 'vehicle', 'order_by', 'deleted_user_id', 'address', 'sr_no', 'from', 'transporter', 'additional_charge', 'notes'];

    protected $casts = [
        'date' => 'datetime',
    ];


    /**
     * Get the created at.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function createdAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::DateFormat($value),
        );
    }

    /**
     * Get the updated at.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function updatedAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::DateFormat($value),
        );
    }

    /**
     * Get the Inward Date.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function date(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::DateFormat($value, config('constant.DATE_FORMAT_SHORT')),
            set: fn ($value) => empty($value) ? null : Helper::convertDateFormat($value, config('constant.DATE_FORMAT_SHORT')),
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
     * Get the Additional Charge.
     *
     * @param  number  $value
     * @return number
     */
    public function additionalCharge(): Attribute
    {
        return new Attribute(
            get: fn ($value) => empty($value) ? '0.00' : $value,
        );
    }
}
