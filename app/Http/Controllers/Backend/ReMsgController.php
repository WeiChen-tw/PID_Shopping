<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\User;
use App\MsgData;
use App\ReMsgData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ReMsgController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $msgData = DB::table('msgData')->paginate(3);
        $reMsgData=DB::table('reMsgData')->get();
        return view('backend.msgBoard',[
            'msgData' => $msgData,
            'reMsgData' => $reMsgData]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        ReMsgData::updateOrCreate(['id' => $request->id],
            [
                'email' => $user->email,
                'content' => $request->content
            ]);

        return response()->json(['success' => '回覆成功']);
    }
    public function destroy($id)
    {

        ReMsgData::find($id)->delete();

        return response()->json(['success' => '成功刪除回覆']);

    }
    public function edit($id)
    {
        $reMsgData = ReMsgData::find($id);

        return response()->json($reMsgData);
    }
    public function deleteUserMsg($id)
    {

        MsgData::find($id)->delete();

        return response()->json(['success' => '成功刪除留言']);

    }
}
