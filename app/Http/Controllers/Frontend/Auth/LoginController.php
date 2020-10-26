<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Frontend\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
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
    protected $redirectTo = '/home';
    public function showLoginForm(Request $request)
    {
        return view('frontend.auth.login');
    }
    protected function credentials(Request $request)
    {
        // $credentials = $request->only($this->username(), 'password');

        // $credentials['banned'] = 'N';
        return array_merge($request->only($this->username(), 'password'), ['banned' => 'N']);
    
        // return $credentials;

    }
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];

        // Load user from database
        $user = User::where($this->username(), $request->{$this->username()})->first();

        // Check if user was successfully loaded, that the password matches
        // and active is not 1. If so, override the default error message.
        if ($user && \Hash::check($request->password, $user->password) && $user->banned != 'N') {
            $errors = [$this->username() => trans('auth.banned')];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required',
             'password' => 'required',
             'captcha' => 'required|captcha',
        ],[
            'captcha.required' => '驗證碼不能為空',
            'captcha.captcha' => '請輸入正確的驗證碼',
        ]);
    }

    

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
