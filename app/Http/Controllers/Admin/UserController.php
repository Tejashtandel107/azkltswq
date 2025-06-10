<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\Admin\UserProfileRequest;
use App\Models\CustomerOrders;
use App\Models\Setting;
use App\Models\User;
use App\Services\CustomerOrderService;
use App\Services\UserService;
use Auth;
use Helper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $customerorder_obj;

    protected $user_obj;

    public function __construct(CustomerOrderService $customerorder, UserService $userObj)
    {
        $this->customerorder_obj = $customerorder;
        $this->user_obj = $userObj;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [
            'pagetitle' => 'All Admins',
            'breadcrumbs' => ['Home' => route('admin.home'), 'All Admins' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'admins',
        ];

        $keyword = $request->input('keyword');
        $queryInstance = User::where('role_id', config('constant.ROLE_ADMIN_ID'));
        $search_fields = ['firstname', 'lastname', 'username', 'email'];

        if ($keyword != '') {
            // SEARCH KEYWORD
            $this->user_obj->filterByKeywords($search_fields, $queryInstance, $keyword);
        }

        $users = $queryInstance->paginate(config('constant.PAGINATION'));

        return view('admin.user.index', $data)->with(compact('users', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Admin',
            'breadcrumbs' => ['Home' => route('admin.home'), 'All Admins' => route('admin.admins'), 'Create' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'admins',
        ];

        return view('admin.user.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $request)
    {
        $type = 'error';
        $message = 'Admin can not created. Please try again.';
        $request_arr = $request->all();
        if ($request->hasFile('photo')) {
            $file_path = Helper::uploadImg($request->file('photo'), config('constant.USER_PROFILE_PATH'));
            if ($file_path) {
                $request_arr['photo'] = $file_path;
            }
        }
        $request_arr['password'] = \Hash::make($request_arr['password']);
        $request_arr['role_id'] = config('constant.ROLE_ADMIN_ID');
        if (User::create($request_arr)) {
            $type = 'success';
            $message = 'Admin created successfully.';
            $redirectUrl = route('admin.admins');

            return response()->json(['message' => $message, 'type' => $type, 'redirectUrl' => $redirectUrl, 'imageLocation' => Helper::getProfileImg($request_arr['photo'])]);

            // return redirect()->route('admin.customers.edit', $request_arr['customer_id'])->with(['type' => 'success','message' => "Customer User created successfully.",]);
        }

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function editAdmin($id)
    {
        $user = User::findOrFail($id);

        $data = [
            'pagetitle' => 'Edit Admin',
            'breadcrumbs' => ['Home' => route('admin.home'), 'All Admins' => route('admin.admins'), 'Edit Admin' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'admins',
        ];
        if ($user) {
            return view('admin.user.create', $data)->with(compact('user'));
        } else {
            abort('403');
        }
    }

    public function updateAdmin(AdminRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to update Admin information. Please try again.';
        // dd($request->all());
        $response = $this->user_obj->update($id, $request);
        if ($response) {
            $type = 'success';
            $message = 'Admin information updated successfully.';
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($response['photo'])]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check()) {
            $data = [
                'pagetitle' => 'My Profile',
                'breadcrumbs' => ['Home' => route('admin.home'), 'My Profile' => ''],
                'menuParent' => 'user',
                'menuChild' => 'user',
            ];

            $user = User::find($id);

            $company = Setting::where('key', config('constant.SETTINGS_KEY'))->first();

            if ($user->user_id == Auth::user()->user_id) {
                return view('admin.user.user', $data)->with(compact('user', 'company'));
            } else {
                abort('403');
            }
        } else {
            abort('403');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserProfileRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to edit user profile. Please try again.';

        $user = User::find($id);

        if ($user->user_id == Auth::user()->user_id) {
            $response = $this->user_obj->update($id, $request);
            if ($response) {
                $type = 'success';
                $message = 'User profile updated successfully.';
            }
            // return redirect()->route('admin.user.edit', $id)->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($response['photo'])]);
    }

    public function updateCompanyINFO(Request $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to edit user profile. Please try again.';

        $request_arr = $request->except(['_method', '_token']);
        $detail = Setting::find($id);
        if ($request->hasFile('logo')) {
            $file_path = Helper::uploadImg($request->file('logo'), config('constant.COMPANY_LOGO_PATH'));
            if ($file_path) {
                $request_arr['logo'] = $file_path;
            }
        } else {
            $value_arr = json_decode($detail->value);
            if (isset($value_arr->logo)) {
                $request_arr['logo'] = $value_arr->logo;
            }
        }

        $detail->value = json_encode($request_arr);
        if ($detail->save()) {
            $type = 'success';
            $message = 'Company details updated successfully.';
            // return redirect()->route('admin.user.edit', $id)->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($request_arr['logo'])]);
    }

    public function storeCompanyINFO(Request $request)
    {
        $type = 'error';
        $message = 'Sorry, failed to edit user profile. Please try again.';

        $request_arr = $request->except(['_method', '_token']);

        if ($request->hasFile('logo')) {
            $file_path = Helper::uploadImg($request->file('logo'), config('constant.COMPANY_LOGO_PATH'));

            if ($file_path) {
                $request_arr['logo'] = $file_path;
            }
        }
        $store_arr['value'] = json_encode($request_arr);
        $store_arr['key'] = config('constant.SETTINGS_KEY');

        if (Setting::create($store_arr)) {
            $type = 'success';
            $message = 'Company details updated successfully.';
            // return redirect()->route('admin.user.edit', Auth::user()->user_id)->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($request_arr['logo'])]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to delete Admin. Please try again.';

            // $collection = $this->customerorder_obj->find("deleted_user_id",$id)->get();
            $collection = CustomerOrders::withTrashed()->where('user_id', $id)->orWhere('deleted_user_id', $id)->get();

            if ($collection->count() > 0) {
                $result = User::find($id)->delete();
            } else {
                $result = User::find($id)->forceDelete();
            }

            if ($result) {
                $type = 'success';
                $message = 'Admin deleted successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }

        // return redirect()->route('admin.user.edit', $id)->with([ 'type' => $type, 'message' => $message ]);
    }

    public function changeAdminPassword(Request $request)
    {
        $type = 'error';
        $message = 'Please enter correct current password';

        if (Auth::Check()) {
            $type = 'error';
            $message = 'Please enter correct current password';

            $this->validate($request, [
                'oldpassword' => ['required', 'current_password'],
                'password' => 'required|min:6|confirmed',
            ]);

            $request_data = $request->all();
            $user = User::findOrFail($request_data['user_id']);
            $current_password = $user->password;

            if (\Hash::check($request_data['oldpassword'], $current_password)) {
                $newPassword = $request_data['password'];
                $user->password = \Hash::make($newPassword);
                if ($user->save()) {
                    $type = 'success';
                    $message = 'Admin password is successfully changed.';
                } else {
                    $type = 'error';
                    $message = 'Sorry, we have encountered an error during password update.';
                }
            }
            // return redirect()->route('admin.user.edit', $user)->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type]);

    }

    /**
     * User can change password from profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $type = 'error';
        $message = 'Please enter correct current password';

        if (Auth::Check()) {
            $type = 'error';
            $message = 'Please enter correct current password';

            $this->validate($request, [
                'oldpassword' => ['required', 'current_password'],
                'password' => 'required|min:6|confirmed',
            ]);

            $user = Auth::User();
            $request_data = $request->all();
            $current_password = $user->password;

            if (\Hash::check($request_data['oldpassword'], $current_password)) {
                $newPassword = $request_data['password'];
                $user->password = \Hash::make($newPassword);
                if ($user->save()) {
                    $type = 'success';
                    $message = 'Your password is successfully changed.';
                } else {
                    $type = 'error';
                    $message = 'Sorry, we have encountered an error during password update.';
                }
            }
            // return redirect()->route('admin.user.edit', $user)->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type]);
    }
}
