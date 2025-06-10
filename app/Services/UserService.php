<?php

namespace App\Services;

use App\Models\User;
use Helper;

class UserService extends BaseService
{
    protected $model;

    public function __construct(User $user_obj)
    {
        $this->model = $user_obj;
    }

    public function update($id, $request)
    {
        $user = User::findOrFail($id);
        $request_arr = $request->all();

        if ($request->hasFile('photo')) {
            $user->deleteFile($user->getRawOriginal('photo'));
            $file_path = Helper::uploadImg($request->file('photo'), config('constant.USER_PROFILE_PATH'));

            if ($file_path) {
                $request_arr['photo'] = $file_path;
            }
        } else {
            if (isset($user->photo)) {
                $request_arr['photo'] = config('constant.USER_PROFILE_PATH').'/'.basename($user->photo);
            }
        }
        if ($user) {
            $user->fill($request_arr);
            if ($user->save()) {
                return $request_arr;
            }

        }

        return false;
    }
}
