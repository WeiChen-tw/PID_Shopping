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
        $request->password = Hash::make('secret');
        if(Hash::check('secret', $request->password)){
            return;
        }
        $validataedData= $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'newPassword' => 'required|string|min:6|confirmed',
        ]);
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
