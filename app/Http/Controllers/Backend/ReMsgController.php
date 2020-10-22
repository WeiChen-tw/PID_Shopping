<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\User;
use App\MsgData;
use App\ReMsgData;
use App\MsgDataDetail;
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
        $msgData = DB::table('msgData')->orderBy('created_at','desc')->paginate(3);
        $reMsgData=DB::table('reMsgData')->orderBy('created_at','desc')->get();
         //DB::enableQueryLog(); // Enable query log
            
        $msgDataDetail = DB::table('msgDataDetails')
            ->join('msgData','msgDataDetails.msgData_id','msgData.id')
            ->orderBy('msgDataDetails.created_at','asc')
            ->select(DB::raw('msgDataDetails.*,msgData.email'))
            ->get();

        if($reMsgData->isEmpty()){
            $reMsgData = 'null';
        }
            //dd(DB::getQueryLog()); // Show results of log
        // $reMsgData = DB::table('msgDataDetails')
        //     ->join('reMsgData','msgDataDetails.msgData_id','reMsgData.id')
        //     ->where('msgDataDetails.auth','admin')
        //     ->orderBy('msgDataDetails.created_at','desc')
        //     ->select(DB::raw('msgDataDetails.*'))
        //     ->get();
        return view('backend.msgBoard')
            ->with('msgData',$msgData)
            ->with('msgDataDetail',$msgDataDetail)
            ->with('reMsgData',$reMsgData);
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        if(!ReMsgData::find($request->id)){
            $msgData = ReMsgData::create([
                'id' => $request->id,
                'email' => $user->email,
            ]);
        }
        if($request->msg_id){
            MsgDataDetail::updateOrCreate(['id' => $request->id],
            [
                'msgData_id' =>$request->msg_id,
                'auth' => 'admin',
                'messageFrom_id' => $user->id,
                'content' => $request->content
            ]);
        }else{
            MsgDataDetail::updateOrCreate(['id' => null],
            [
                'msgData_id' =>$request->id,
                'auth' => 'admin',
                'messageFrom_id' => $user->id,
                'content' => $request->content
            ]);
        }
        
        return response()->json(['success' => '回覆成功']);
    }
    public function destroy($id)
    {

        MsgDataDetail::find($id)->delete();
        
        return response()->json(['success' => '成功刪除回覆']);

    }
    public function edit($id)
    {
        $reMsgData = MsgDataDetail::find($id);

        return response()->json($reMsgData);
    }
    public function deleteUserMsg($id)
    {

        MsgData::find($id)->delete();
        ReMsgData::find($id)->delete();
        return response()->json(['success' => '成功刪除留言']);

    }
}
