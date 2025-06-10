<?php

namespace App\Services;

use App\Models\Item;

class ItemService extends BaseService
{
    protected $model;

    public function __construct(Item $item_obj)
    {
        $this->model = $item_obj;
    }

    public function select($key = '', $orderBy = 'asc')
    {
        if (! empty($key)) {
            return $this->model->Active()->orderBy($key, $orderBy)->get()->toArray();
        }

        return false;
    }
}
