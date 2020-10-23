<?php

namespace App\Http\Controllers\Backend;

use App\Discount;
use App\Models\Config;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\Products_Discounts;
use App\ShopCart;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class myDiscount
{
    public $id;
    public $sysMethod;
    public $sysTotal;
    public $sysDiscount;
    public $orderDiscount;
}
class OrderDetailAjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        return;

    }

    
    public function getOrder(Request $request)
    {
        if ($request->ajax()) {
            //$user_id = $request->user()->id;
            $data = Order::get();
            

            // Your Eloquent query executed by using get()

            $order = DB::table('orders')
                ->join('orderDetails', 'orders.id', 'orderDetails.id')
                ->join('users', 'orders.user_id', 'users.id')
                ->select('orders.id', 'orders.user_id', 'users.name', 'orders.created_at', 'orders.status','orders.sysMethod','orders.orderDiscount', DB::raw('SUM(orderDetails.price * orderDetails.quantity) as total'))
                ->groupBy('orders.id')
                ->orderBy('orders.id', 'desc')
                ->get();
            //$data2 = DB::select('SELECT p.productID,c.id,p.name,c.name as category FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID ,c.id,c.name');
            
            return Datatables::of($order)
                ->addColumn('details', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="Details" class="btn btn-primary btn-sm details-control">明細</a>';
                    return $btn;
                })

                ->addColumn('action', function ($row) {
                    $actionText = "";

                    switch ($row->status) {
                        case '待出貨':
                            $actionText = "出貨";
                            break;
                        case '待退貨':
                            $actionText = "同意退貨";
                            break;
                        default:
                            return;
                            break;
                    }
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText . '" class="btn btn-danger btn-sm ' . $actionText . '">' . $actionText . '</a>';
                    if ($actionText === "同意退貨") {
                        $actionText2 = "拒絕退貨";
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText2 . '" class="btn btn-success btn-sm ' . $actionText2 . '">' . $actionText2 . '</a>';

                    }
                    return $btn;

                })

                ->rawColumns(['details', 'action'])
                ->make(true);

        }

        // return view('frontend.home', compact('category'));

    }

    public function getOrderDetail(Request $request)
    {
        if ($request->ajax()) {
            //$user_id = $request->user()->id;
            $id = $request->id;
            //DB::enableQueryLog(); // Enable query log

            $orderDetails = DB::table('orderDetails')
                ->where('orderDetails.id', $id)
                ->select('orderDetails.*', DB::raw('SUM(orderDetails.price * orderDetails.quantity) as total'))
                ->groupBy('orderDetails.id',
                    'orderDetails.user_id',
                    'orderDetails.status',
                    'orderDetails.name',
                    'orderDetails.quantity',
                    'orderDetails.price',
                    'orderDetails.discount_flag',
                    'orderDetails.created_at',
                    'orderDetails.updated_at',
                    'orderDetails.deleted_at',
                    'orderDetails.productID')
                ->get();
            //dd(DB::getQueryLog()); // Show results of log
            return response()->json($orderDetails);

        }

        // return view('frontend.home', compact('category'));

    }




    public function ship(Request $request)
    {
        DB::transaction(function() use($request){
            $order = Order::find($request->id);
            $order->status = "已出貨";
            $order->save();
            $orderDetail = OrderDetail::where('id', $request->id)->get();
            foreach ($orderDetail as $key => $row) {
                $row->status = "已出貨";
                $row->save();
            }
        });
        
        return response()->json(['success' => '出貨成功']);
    }
    public function returnOrder(Request $request)
    {
        
            //同意退貨
        if ($request->action == 'yes') {
            $config = Config::find(1);
            $order = Order::find($request->id);
            $orderDetail = OrderDetail::where('id', $request->id)->get();
            $user = User::find($order->user_id);
            $product_id = null;
            $amount = 0;
            $exp = null;
            $exp_max = $config->moneyToLevel * $config->upgrade_limit;
            if ($request->productID) {
                $product_id = $request->productID;
            }
            //計算訂單總額
            foreach ($orderDetail as $key => $row) {
                $amount += $row->quantity * $row->price;
            }
            //扣除優惠活動所折抵掉的金額
            if ($order->sysMethod == 2) {
                $amount -= $order->orderDiscount;
            }
            //設定經驗為可接受最大值
            if ($amount > $exp_max) {
                $exp = $exp_max;
            }
            if ($order->sysMethod == 1) {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    if ($lv - $user->level <= $config->upgrade_limit) {
                        $user->level -= $config->upgrade_limit;
                    } else {
                        $user->level = $lv;
                    }
                }
                $user->coin -= $order->orderDiscount;
                $user->coin += $amount;
                DB::transaction(function() use($user,$order,$orderDetail){
                    $user->save();
                    $order->status = "退貨成功";
                    $order->save();
                    foreach ($orderDetail as $key => $row) {
                        $product = Product::find($row->productID);
                        $product->quantity += $row->quantity;
                        $product->quantitySold -= $row->quantity;
                        $product->save();
                        $row->status = "退貨成功";
                        $row->save();
                    }
                });
                
                return response()->json(['success' => '退貨成功,系統收回$' . $order->orderDiscount . '購物金與經驗值' . $amount]);
            } else if ($order->sysMethod == 2) {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                $user->coin += $amount;
                DB::transaction(function() use($user,$order,$orderDetail){
                    $user->save();
                    $order->status = "退貨成功";
                    $order->save();
                    foreach ($orderDetail as $key => $row) {
                        $product = Product::find($row->productID);
                        $product->quantity += $row->quantity;
                        $product->quantitySold -= $row->quantity;
                        $product->save();
                        $row->status = "退貨成功";
                        $row->save();
                    }
                });
                
                return response()->json(['success' => '退貨成功,系統收回經驗值' . $amount]);
            } else {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                $user->coin += $amount;
                DB::transaction(function() use($user,$order,$orderDetail){
                    $user->save();
                    $order->status = "退貨成功";
                    $order->save();
                    foreach ($orderDetail as $key => $row) {
                        $product = Product::find($row->productID);
                        $product->quantity += $row->quantity;
                        $product->quantitySold -= $row->quantity;
                        $product->save();
                        $row->status = "退貨成功";
                        $row->save();
                    }
                });
              
                return response()->json(['success' => '退貨成功,系統收回經驗值' . $amount]);
            }
            //$order->delete();
            return response()->json(['success' => '退貨成功']);
        } else if ($request->action == 'no') {
            $order = Order::find($request->id);
            $order->status = "退貨失敗";
            $order->save();
            foreach ($orderDetail as $key => $row) {
                $row->status = "退貨失敗";
                $row->save();
            }
            return response()->json(['success' => '拒絕退貨成功']);
        }
    
        
    }
    public function returnOrderDetail(Request $request)
    {
        
        if ($request->action == 'yes') {
            $config = Config::find(1);
            $order = Order::find($request->id);
            $orderDetail = OrderDetail::where('id', $request->id)->get();
            $user = User::find($order->user_id);
            $product_id = null;
            $amount = 0;
            $exp = null;
            $sum = null;
            $this_item = DB::table('orderDetails')
                ->where('id','=',$request->id)
                ->where('productID','=',$request->product_id)
                ->first();
            $exp_max = $config->moneyToLevel * $config->upgrade_limit;
            if ($request->product_id) {
                $product_id = $request->product_id;
            }
            //計算退貨商品金額
            foreach ($orderDetail as $key => $row) {
                if ($product_id) {
                    if ($row->productID == $product_id) {
                        $amount += $row->quantity * $row->price;
                    }else{
                        $sum += $row->quantity*$row->price;
                    }
                }
            }
            $sum += $amount;
            //優惠模式2 要計算折扣數
            if ($order->sysMethod == 2) {
                //計算折扣數
                if($this_item->discount_flag==1){
                    $amount = round($amount  *(1 - $order->sysDiscount/100));
                }
            } 
            //設定經驗為可接受最大值 
            //計算當筆訂單總額
            // 回朔等級 重算訂單經驗值
            if ($sum > $exp_max) {
                $exp = $exp_max;
            }else{
                $exp = $sum;
            }
            if($sum-=$amount > $exp_max){
                $sum = $exp_max;
            }
            //判斷優惠模式
            if ($order->sysMethod == 1) {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel) + $sum;

                if ($lv >= 0 && $user->level < 10) {
                    if ($lv - $user->level <= $config->upgrade_limit) {
                        $user->level -= $config->upgrade_limit;
                    } else {
                        $user->level = $lv;
                    }
                }
                $user->coin += $amount;
                DB::transaction(function() use($request,$user,$order,$this_item){
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();
                    
                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                    ->where('id','=',$request->id)
                    ->where('productID','=',$request->product_id)
                    ->update(['status' => "退貨成功"]);
                });
                
                return response()->json(['success' => '退貨成功,系統退回$' . $amount  . '購物金與重新計算獲得經驗值' . $sum]);
            } else if ($order->sysMethod == 2) {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel) + $sum;
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                $user->coin += $amount;
                DB::transaction(function() use($request,$user,$order,$this_item){
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();
                
                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                    ->where('id','=',$request->id)
                    ->where('productID','=',$request->product_id)
                    ->update(['status' => "退貨成功"]);
                    
                });
                
                return response()->json(['success' => '退貨成功,退回購物金$'.$amount.',系統重新計算訂單經驗值' . $sum]);
            } else {
                $user->exp_bar -= $exp;
                $lv = round($user->exp_bar / $config->moneyToLevel) + $sum;
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                $user->coin += $amount;
                DB::transaction(function() use($request,$user,$order,$this_item){
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();
                
                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                    ->where('id','=',$request->id)
                    ->where('productID','=',$request->product_id)
                    ->update(['status' => "退貨成功"]);
                    
                });
                
                return response()->json(['success' => '退貨成功,退回購物金$'.$amount.',系統重新計算訂單經驗值' . $sum]);
            }
            return response()->json(['success' => '退貨成功']);
        } else if ($request->action == 'no') {
            DB::transaction(function() use($order,$orderDetail){
                $order = Order::find($request->id);
                $order->status = "退貨失敗";
                $order->save();
                foreach ($orderDetail as $key => $row) {
                    $row->status = "退貨失敗";
                    $row->save();
                }
            });
            
            return response()->json(['success' => '拒絕退貨成功']);
        }
    }

    
}
