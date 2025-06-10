<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the failed login response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // return back()->withInput()->withErrors (['type' => 'error', 'message' => "Please enter valid login details."]);;
        return response()->json(['message' => 'Please enter valid username or password', 'type' => 'error']);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL) ? $this->username() : 'username';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }

    /**
     * The user has been authenticated.
     *
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->isactive) {
            $role_id = $user->getRoleId();
            if (! empty(session('url.intended'))) {
                // return redirect()->intended($this->redirectPath());
                return response()->json(['message' => 'Login successfully. Please wait..', 'type' => 'success', 'redirectUrl' => route('user.home')]);
            } elseif ($role_id == config('constant.ROLE_ADMIN_ID') || $role_id == config('constant.ROLE_SUPER_ADMIN_ID')) {
                // return redirect()->route('admin.home');
                return response()->json(['message' => 'Login successfully. Please wait..', 'type' => 'success', 'redirectUrl' => route('admin.home')]);
            } else {
                // return redirect($this->redirectPath());
                return response()->json(['message' => 'Login successfully. Please wait..', 'type' => 'success', 'redirectUrl' => route('user.home')]);
                // return redirect($this->redirectPath());
            }
        } else {
            // Auth::logout ();
            // $errors = [$this->username() => trans('auth.inactive')];
            // return redirect()->back()->withInput($request->only($this->username(), 'remember'))->withErrors($errors);
            Auth::logout();

            return response()->json(['message' => 'Sorry, your account is inactive currently. Please contact administrator.', 'type' => 'error']);
            // return back()->withInput()->withErrors (['type' => 'error', 'message' => "Sorry, your account is inactive currently. Please contact administrator.",]);
        }
    }
}
