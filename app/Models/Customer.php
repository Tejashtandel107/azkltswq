<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['companyname', 'address', 'gstnumber', 'contact_person', 'phone', 'photo', 'isactive', 'last_invoice_date', 'invoice_limit'];

    /**
     * Set the Last Invoice Date.
     *
     * @param  timestamp  $value
     * @return string
     */
    public function lastInvoiceDate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::DateFormat($value, config('constant.DATE_FORMAT_SHORT')),
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }

                return Helper::convertDateFormat($value, config('constant.DATE_FORMAT_SHORT'));
            }
        );
    }

    /**
     * Scope a query to only include active items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeActive($query)
    {
        return $query->where('isactive', config('constant.status.active'));
    }

    /**
     * Get the Company name.
     *
     * @return {companyname}
     */
    public function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => "{$this->companyname}",
        );
    }

    /**
     * Get the user's photo.
     *
     * @param  string  $value
     * @return string
     */
    public function photo(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Helper::getProfileImg($value),
        );
    }

    /**
     * Delete File from storage
     */
    public function deleteFile($file = '')
    {
        return Storage::delete($file);
    }
}
