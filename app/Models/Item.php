<?php

namespace App\Models;

use Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'isactive'];

    /**
     * Get the item's created at.
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
     * Get the item's updated at.
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
     * Scope a query to only include active items.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeActive($query)
    {
        return $query->where('isactive', config('constant.status.active'));
    }
}
