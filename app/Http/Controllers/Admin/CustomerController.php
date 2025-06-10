<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\customerUserRequest;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerOrderService;
use App\Services\CustomerService;
use App\Services\UserService;
use Auth;
use Helper;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customer_obj;

    protected $user_obj;

    protected $customerorder_obj;

    public function __construct(CustomerService $customer, CustomerOrderService $customerorder, UserService $userObj)
    {
        $this->customer_obj = $customer;
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
            'pagetitle' => 'All Customers',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Customers' => route('admin.customers.index')],
            'menuParent' => 'customers',
            'menuChild' => 'customers',
        ];

        $keyword = $request->input('keyword');
        $queryInstance = Customer::whereNull('customers.deleted_at');
        $search_fields = ['companyname', 'address', 'phone'];

        if ($keyword != '') {
            // SEARCH KEYWORD
            $this->customer_obj->filterByKeywords($search_fields, $queryInstance, $keyword);
        }

        $customers = $queryInstance->orderBy('companyname', 'asc')->paginate(config('constant.PAGINATION'));

        return view('admin.customers.index', $data)->with(compact('customers', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pagetitle' => 'Create Customer',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Customers' => route('admin.customers.index'), 'Create' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'customers',
        ];

        return view('admin.customers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $type = 'error';
        $message = 'Customer can not created. Please try again.';
        $request_arr = $request->all();
        if ($request->hasFile('photo')) {
            $file_path = Helper::uploadImg($request->file('photo'), config('constant.CUSTOMER_PROFILE_PATH'));

            if ($file_path) {
                $request_arr['photo'] = $file_path;
            }
        }
        if ($this->customer_obj->store($request_arr)) {
            $type = 'success';
            $message = 'Customer created successfully.';
            $redirectUrl = route('admin.customers.index');

            // return redirect()->route('admin.customers.index')->with(['type' => 'success','message' => "Customer created successfully."]);
            return response()->json(['message' => $message, 'type' => $type, 'redirectUrl' => $redirectUrl, 'imageLocation' => Helper::getProfileImg($request_arr['photo'])]);
        }

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function createUser($id)
    {
        $data = [
            'pagetitle' => 'Create Customer User',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Customers' => route('admin.customers.index'), 'Edit Customers' => route('admin.customers.edit', $id), 'Create Customer User' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'customers',
        ];
        $customer_id = $id;

        $customers = Customer::Active()->orderBy('companyname', 'asc')->get();

        return view('admin.customers.createuser', $data)->with(compact('customer_id', 'customers'));
    }

    public function storeUser(customerUserRequest $request)
    {
        $type = 'error';
        $message = 'Customer user can not created. Please try again.';
        $request_arr = $request->all();
        if ($request->hasFile('photo')) {
            $file_path = Helper::uploadImg($request->file('photo'), config('constant.USER_PROFILE_PATH'));
            if ($file_path) {
                $request_arr['photo'] = $file_path;
            }
        }
        $request_arr['password'] = \Hash::make($request_arr['password']);
        $request_arr['role_id'] = config('constant.ROLE_USER_ID');
        if (User::create($request_arr)) {
            $type = 'success';
            $message = 'Customer User created successfully.';
            $redirectUrl = route('admin.customers.edit', $request_arr['customer_id']);

            return response()->json(['message' => $message, 'type' => $type, 'redirectUrl' => $redirectUrl, 'imageLocation' => Helper::getProfileImg($request_arr['photo'])]);

            // return redirect()->route('admin.customers.edit', $request_arr['customer_id'])->with(['type' => 'success','message' => "Customer User created successfully.",]);
        }

        return response()->json(['message' => $message, 'type' => $type]);

        // return redirect()->back()->with(['type' => 'error','message' => "Customer user can not created. Please try again."]);
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
        $data = [
            'pagetitle' => 'Edit Customer',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Customers' => route('admin.customers.index'), 'Edit' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'customers',
        ];

        $customer = Customer::findOrFail($id);

        $users = User::where('customer_id', $id)->get();
        if ($customer) {
            return view('admin.customers.create', $data)->with(compact('customer', 'users'));
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
    public function update(CustomerRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to update customer. Please try again.';
        $response = $this->customer_obj->update($id, $request);
        if ($response) {
            $type = 'success';
            $message = 'Customer updated successfully.';
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($response['photo'])]);
        // return redirect()->route('admin.customers.edit', $id)->with([ 'type' => $type, 'message' => $message ]);
    }

    public function editUser($id)
    {

        $user = User::findOrFail($id);
        $customer_id = $user->customer_id;

        $data = [
            'pagetitle' => 'Edit Customer User',
            'breadcrumbs' => ['Home' => route('admin.home'), 'Customers' => route('admin.customers.index'), 'Edit Customers' => route('admin.customers.edit', $customer_id), 'Edit Customer User' => ''],
            'menuParent' => 'customers',
            'menuChild' => 'customers',
        ];

        $customers = Customer::Active()->orderBy('companyname', 'asc')->get();
        if ($user) {
            return view('admin.customers.createuser', $data)->with(compact('user', 'customer_id', 'customers'));
        } else {
            abort('403');
        }
    }

    public function updateUser(customerUserRequest $request, $id)
    {
        $type = 'error';
        $message = 'Sorry, failed to update customer user. Please try again.';
        // dd($request->all());
        $response = $this->user_obj->update($id, $request);
        if ($response) {
            $type = 'success';
            $message = 'Customer user updated successfully.';
        }

        return response()->json(['message' => $message, 'type' => $type, 'imageLocation' => Helper::getProfileImg($response['photo'])]);
        // return redirect()->route('admin.user.edit', $id)->with([ 'type' => $type, 'message' => $message ]);
    }

    public function destroyUser(Request $request, $id)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to delete User. Please try again.';

            $result = User::find($id)->delete();

            if ($result) {
                $type = 'success';
                $message = 'User deleted successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
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
            $message = 'Sorry, failed to delete customer. Please try again.';
            $collection = $this->customerorder_obj->find('customer_id', $id)->withTrashed()->get();

            if ($collection->count() > 0) {
                $result = Customer::find($id)->delete();
            } else {
                $result = Customer::find($id)->forceDelete();
            }

            if ($result) {
                $type = 'success';
                $message = 'Customer deleted successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message]);
        }
    }

    /**
     * open modal to create new Customer
     *
     * @return \Illuminate\Http\Response
     */
    public function openModal(Request $request)
    {
        $modaltitle = 'Add New Customer';
        [, , , , , $customers] = $this->customer_obj->pluckData(['customer_id']);

        return view('admin.modal.customers.create', compact('modaltitle', 'request', 'customers'));
    }

    /**
     * Saves new Customer via modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveModal(CustomerRequest $request)
    {
        if ($request->ajax()) {
            $type = 'error';
            $message = 'Sorry, failed to create customer. Please try again.';
            $result = $this->customer_obj->store($request->all());

            if ($result) {
                $type = 'success';
                $message = 'New customer created successfully.';
            }

            return response()->json(['type' => $type, 'message' => $message, 'customers' => $result]);
        }
    }

    public function changePassword(Request $request)
    {
        $type = 'error';
        $message = 'Please enter correct current password';

        if (Auth::Check()) {
            // $user = Auth::User();
            $request_data = $request->all();
            $user = User::findOrFail($request_data['user_id']);
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
            // return redirect()->route('admin.user.edit', $user->user_id)->with([ 'type' => $type, 'message' => $message ]);
            // return redirect()->back()->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type]);
    }
}
