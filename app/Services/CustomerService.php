<?php

namespace App\Services;

use App\Models\Customer;
use Helper;

class CustomerService extends BaseService
{
    protected $model;

    public function __construct(Customer $customer_obj)
    {
        $this->model = $customer_obj;
    }

    public function update($id, $request)
    {
        $customer = Customer::findOrFail($id);
        $request_arr = $request->all();

        if ($request->hasFile('photo')) {
            $customer->deleteFile($customer->getRawOriginal('photo'));
            $file_path = Helper::uploadImg($request->file('photo'), config('constant.CUSTOMER_PROFILE_PATH'));
            if ($file_path) {
                $request_arr['photo'] = $file_path;
            }
        } else {
            if (isset($customer->photo)) {
                $request_arr['photo'] = config('constant.CUSTOMER_PROFILE_PATH').'/'.basename($customer->photo);
            }
        }
        if ($customer) {
            $customer->fill($request_arr);
            if ($customer->save()) {
                return $request_arr;
            }
        }

        return false;
    }
}
