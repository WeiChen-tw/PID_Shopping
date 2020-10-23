<?php
namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Frontend\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class LoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/home';
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }
    public function showLoginForm()
    {
        return view('backend.auth.login');
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
    //指定guard admin -> 對應 config/auth.php 設定的'admin'參數（含資料表）
    protected function guard()
    {
        return Auth::guard('admin');
    }
}
