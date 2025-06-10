<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grid extends Model
{
    use SoftDeletes;

    protected $table = 'grid';

    protected $primaryKey = 'grid_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['grid_id', 'number'];
}
