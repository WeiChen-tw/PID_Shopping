<?php

namespace App\Http\Controllers\Backend;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function setConfig(Request $request)
    {
        if (!is_numeric($request->moneyToLevel) || !is_numeric($request->upgrade_limit)) {
            return response()->json(['error' => '僅能輸入數字']);
        }
        if ($request->upgrade_limit <=0 || $request->moneyToLevel <=0) {
            return response()->json(['error' => '請輸入大於0的數字']);
        }
        if ($request->upgrade_limit > 10) {
            return response()->json(['error' => '單筆訂單升級限制不得大於10等']);
        }
        if ($request->upgrade_limit <= 0) {
            return response()->json(['error' => '單筆訂單升級限制最小為1等']);
        }
        
        Config::updateOrCreate(['id' => 1],
            ['moneyToLevel' => $request->moneyToLevel,
                'upgrade_limit' => $request->upgrade_limit,
            ]);
        return response()->json(['success' => '參數設定成功']);
    }
    public function getConfig(Request $request)
    {
        $config = Config::find(1);
        if($config){
            return response()->json([
                'success' => '載入目前參數',
                'moneyToLevel' => $config->moneyToLevel,
                'upgrade_limit' => $config->upgrade_limit]);
        }
    }
}
