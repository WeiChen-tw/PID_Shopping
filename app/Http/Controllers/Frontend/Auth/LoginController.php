<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Frontend\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $credentials = $request->only($this->username(), 'password');

        $credentials['banned'] = 'N';

        return $credentials;

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
