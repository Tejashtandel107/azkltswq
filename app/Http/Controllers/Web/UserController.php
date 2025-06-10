<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UserProfileRequest;
use App\Models\Customer;
use App\Models\User;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $user_obj;

    public function __construct(UserService $user)
    {
        $this->user_obj = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit()
    {
        if (Auth::check()) {
            $data = [
                'pagetitle' => 'My Profile',
                'breadcrumbs' => ['Home' => route('user.home'), 'My Profile' => ''],
                'menuParent' => 'user',
                'menuChild' => 'user',
            ];

            // $user = User::find($id);
            $user = Auth::User();
            $customer = Customer::where('customer_id', $user->customer_id)->Active()->orderBy('companyname', 'asc')->first();
            if ($user->user_id == Auth::user()->user_id) {
                return view('web.user.user', $data)->with(compact('user', 'customer'));
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
        $message = 'Sorry, failed to edit your profile. Please try again.';

        $user = User::find($id);

        if ($user->user_id == Auth::user()->user_id) {
            $response = $this->user_obj->update($id, $request);
            if ($response) {
                $type = 'success';
                $message = 'User profile updated successfully.';
            }

            // return redirect()->route('user.profile.edit')->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * User can change password from profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $type = 'error';
        $message = 'Sorry, failed to update your password. Please try again.';

        if (Auth::Check()) {
            $type = 'error';
            $message = 'Please enter correct current password';

            $this->validate($request, [
                'oldpassword' => 'required|current_password',
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
            // return redirect()->route('user.profile.edit')->with([ 'type' => $type, 'message' => $message ]);
        }

        return response()->json(['message' => $message, 'type' => $type]);

    }
}
