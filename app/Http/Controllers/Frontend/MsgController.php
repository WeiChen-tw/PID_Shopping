<?php

namespace App\Http\Controllers\Frontend;

use App\MsgData;
use App\MsgDataDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MsgController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $msgData = DB::table('msgData')->orderBy('created_at', 'desc')->paginate(3);
        // $reMsgData=DB::table('reMsgData')->orderBy('created_at','desc')->get();
        //DB::enableQueryLog(); // Enable query log

        $msgDataDetail = DB::table('msgDataDetails')
            ->join('msgData', 'msgDataDetails.msgData_id', 'msgData.id')
            ->orderBy('msgDataDetails.created_at', 'asc')
            ->select(DB::raw('msgDataDetails.*,msgData.email'))
            ->get();
        //dd(DB::getQueryLog()); // Show results of log
        $reMsgData = DB::table('msgDataDetails')
            ->join('reMsgData', 'msgDataDetails.msgData_id', 'reMsgData.id')
            ->where('msgDataDetails.auth', 'admin')
            ->orderBy('msgDataDetails.created_at', 'desc')
            ->select(DB::raw('msgDataDetails.*'))
            ->get();
        return view('frontend.msgBoard')
            ->with('msgData', $msgData)
            ->with('msgDataDetail', $msgDataDetail)
            ->with('reMsgData', $reMsgData);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!MsgData::find($request->id)) {
            $msgData = MsgData::create([
                'id' => $request->id,
                'email' => $user->email,
            ]);
            MsgDataDetail::updateOrCreate(['id' => null],
                [
                    'msgData_id' => $msgData->id,
                    'auth' => 'user',
                    'messageFrom_id' => $user->id,
                    'content' => $request->content,
                ]);
        } else {
            MsgDataDetail::updateOrCreate(['id' => null],
                [
                    'msgData_id' => $request->id,
                    'auth' => 'user',
                    'messageFrom_id' => $user->id,
                    'content' => $request->content,
                ]);
        }
        return response()->json(['success' => '留言成功']);
    }
    public function destroy($id)
    {

        MsgData::find($id)->delete();
        ReMsgData::find($id)->delete();
        return response()->json(['success' => '成功刪除回覆']);

    }
    public function edit($id)
    {
        $msgData = MsgDataDetail::where('id', $id)->first();

        return response()->json($msgData);
    }
    public function getMsg($id)
    {
        $msgData = MsgDataDetail::where('msgData_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($msgData);
    }
    public function reply(Request $request)
    {
        //$user = Auth::user();
        MsgDataDetail::updateOrCreate(['id' => null],
            [
                'msgData_id' => $request->id,
                'auth' => 'user',
                'messageFrom_id' => $request->user()->id,
                'content' => $request->content,
            ]);
        return response()->json(['success' => '回覆成功']);
    }
    public function editMsg(Request $request)
    {
        //$user = Auth::user();
        MsgDataDetail::updateOrCreate(['id' => $request->id],
            [
                'msgData_id' => $request->msg_id,
                'auth' => 'user',
                'messageFrom_id' => $request->user()->id,
                'content' => $request->content,
            ]);
        return response()->json(['success' => '回覆成功']);
    }
}
