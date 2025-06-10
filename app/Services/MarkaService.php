<?php

namespace App\Services;

use App\Models\Marka;

class MarkaService extends BaseService
{
    protected $model;

    public function __construct(Marka $marka_obj)
    {
        $this->model = $marka_obj;
    }
}
