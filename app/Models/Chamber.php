<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chamber extends Model
{
    use SoftDeletes;

    protected $table = 'chamber';

    protected $primaryKey = 'chamber_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['chamber_id', 'number'];
}
