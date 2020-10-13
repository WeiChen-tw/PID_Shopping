<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $id = $request->user()->id;
        //$users = Profile::find($id);
        $profile = Profile::select('name','email','addr','phone','coin','experience')
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
            ]);
    
            Profile::updateOrCreate(['id' => $id],
                [  'name' => $request->name,
                    'addr' => $request->addr,
                    'phone' => $request->phone,
                ]
            );
            return redirect('home')->withSuccess('Profile saved successfully.');
             
        }else{
            $res = DB::table('users')->where('id',$id)->select('password')->first();
            if(!Hash::check($request->oldPassword,$res->password)){
                return redirect('home')->withErrors(['oldPassword'=>'The original passowrd is wrong.']);
             }
            $validataedData= $request->validate([
                'name' => 'required|string|max:255',
                'oldPassword' => 'required|string|min:6|',
                'newPassword' => 'required|string|min:6|confirmed',
            ]);
    
            Profile::updateOrCreate(['id' => $id],
                [  'name' => $request->name,
                    'addr' => $request->addr,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->newPassword),
                ]
            );
            return redirect('home')->withSuccess('Profile && New Password saved successfully.');
             
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

        return response()->json(['success' => 'Profile saved successfully.']);
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Profile  $users

     * @return \Illuminate\Http\Response

     */

    public function edit(Request $request)
    {
        // $id = $request->user()->id;
        // //$users = Profile::find($id);
        // $users = Profile::where('id', $id)->first();
        
        // //$users->save();
        // return response()->json($users);
    }

    

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Profile  $users

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        Profile::find($id)->delete();

        return response()->json(['success' => 'Profile deleted successfully.']);

    }

}
