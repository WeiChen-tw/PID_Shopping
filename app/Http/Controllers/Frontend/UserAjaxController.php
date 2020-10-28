<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserAjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkstatus');
    }
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $id = $request->user()->id;
        //$users = Profile::find($id);
        $profile = Profile::select('name','email','addr','phone','coin','level','exp_bar')
            ->where('id', $id)->first();
        
        //$users->save();
        return response()->json($profile);
        
    }
    
   
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */
    public function editProfile(Request $request)
    {
        $id = $request->user()->id;
        //$request->oldPassword = Hash::make('secret');
        //使用者沒有輸入原密碼,所以不對密碼欄位進行驗證
        if($request->oldPassword === null){
            $validataedData= $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|regex:/^09[0-9]{8}$/',
                'addr' => 'required|string|max:255',
            ],
            [
                'phone.regex' => '請輸入正確的手機號碼',
            ]);
    
            Profile::updateOrCreate(['id' => $id],
                [  'name' => $request->name,
                    'addr' => $request->addr,
                    'phone' => $request->phone,
                ]
            );
            return redirect('home')->withSuccess('成功修改個人資料.');
             
        }else{
            $res = DB::table('users')->where('id',$id)->select('password')->first();
            if(!Hash::check($request->oldPassword,$res->password)){
                return redirect('home')->withErrors(['oldPassword'=>'原密碼錯誤.']);
             }
            $validataedData= $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|regex:/^09[0-9]{8}$/',
                'addr' => 'required|string|max:255',
                'oldPassword' => 'required|string|min:6|',
                'newPassword' => 'required|string|min:6|confirmed',
            ],
            [
                'phone.regex' => '請輸入正確的手機號碼',
            ]);
    
            Profile::updateOrCreate(['id' => $id],
                [  'name' => $request->name,
                    'addr' => $request->addr,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->newPassword),
                ]
            );
            return redirect('home')->withSuccess('個人資料及密碼修改成功');
             
            //return response()->json(['success' => 'Profile && New Password saved successfully.']);
        }
  

        
    }
    public function store(Request $request)
    {

        Profile::updateOrCreate(['id' => $request->id],
            [  'name' => $request->name,
                'email' => $request->email,
                'addr' => $request->addr,
                'phone' => $request->phone,
                //'password' => bcrypt($request->password),
            ]);

        return response()->json(['success' => '成功修改個人資料.']);
    }


    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Profile  $users

     * @return \Illuminate\Http\Response

     */

    // public function destroy($id)
    // {

    //     Profile::find($id)->delete();

    //     return response()->json(['success' => '成功刪除個人資料.']);

    // }

}
