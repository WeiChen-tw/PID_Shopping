<?php

namespace App\Http\Controllers\Frontend;

use App\Discount;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\Products_Discounts;
use App\ShopCart;
use App\User;
use App\Models\Config;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class myOrder
{
    public $id;
    public $sysMethod;
    public $sysTotal;
    public $sysDiscount;
    public $orderDiscount;
    public $amount;
}
class OrderDetailAjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {
        
        $max_id = DB::select('select max(id) as id from `orders`');
        $id = $max_id[0]->id + 1;
        $user_id = $request->user()->id;
        $wrong_id = null;
        $addr = $request->addr;
        $user =  User::find($request->user()->id);
        $use_coin = null;
        if($request->coin){
            $use_coin =$request->coin;
        }
        foreach ($request->quantity as $key => $quantity) {
            if ($quantity <= 0) {
                $wrong_id .= $request->productID[$key] . ' ';
            }
        }
        if ($wrong_id !== null) {
            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量錯誤']);
        }
        foreach ($request->quantity as $key => $quantity) {
            $product = Product::find($request->productID[$key]);
            if ($product->quantity < $quantity) {
                $wrong_id .= $request->productID[$key] . ' ';
                $shopcart = ShopCart::where('user_id',$user_id)
                    ->where('productID', $request->productID[$key])
                    ->delete();
            }
        }
        if ($wrong_id !== null) {

            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量不足,自動移出購物車']);
        }
        if (!isset($addr)) {
            return response()->json(['wrong' => '請輸入地址']);
        }
        if ($use_coin!=null && $use_coin > $user->coin ) {
            return response()->json(['wrong' => '購物金錯誤']);
        }
        //----計算優惠開始----//
        $discount_method = [];
        $order_total = [];
        $order_discount = [];
        $other_sum = [];
        $discounts_id = [];
        $sys_total = [];
        $sys_discount = [];
        $other_total = 0;
        $amount = [];
        
        foreach ($request->quantity as $key => $quantity) {
            if ($quantity <= 0) {
                $wrong_id .= $request->productID[$key] . ' ';
            }
        }
        if ($wrong_id !== null) {
            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量錯誤']);
        }

        //遍歷購買產品陣列
        foreach ($request->productID as $key => $product_id) {
            $products_discounts = Products_Discounts::where('product_id', '=', $product_id)->get();
            $product = Product::find($product_id);
            $other_total += $product->price * $request->quantity[$key];
            if ($products_discounts) {
                //遍歷優惠活動陣列
                foreach ($products_discounts as $key2 => $pd) {
                    $discount = Discount::find($pd->discount_id);
                    if($user->level<$discount->user_lv){
                        continue;
                    }
                    //新增優惠
                    if (!in_array($pd->discount_id, $discounts_id)) {
                        //紀錄優惠ID
                        array_push($discounts_id, $pd->discount_id);
                        $discount = Discount::find($pd->discount_id);
                        
                        //紀錄優惠模式
                        array_push($discount_method, $discount->method);
                        array_push($sys_total, $discount->total);
                        array_push($sys_discount, $discount->discount);
                        //紀錄消費金額
                        array_push($order_total, $product->price * $request->quantity[$key]);
                        array_push($order_discount, 0);
                        array_push($amount, 0);
                        array_push($other_sum, 0);
                        // if($order_total>=$pd->total){
                        //     array_push($order_discount,$discount->discount);
                        // }
                    } else {
                        $idx = array_search($pd->discount_id, $discounts_id);
                        //加總優惠活動消費金額
                        $order_total[$idx] += $product->price * $request->quantity[$key];
                    }
                }
            }
        }
        $oneHundred = 100;
        $base = 0;
        $discount_flag = '';
        //計算累計消費金額與可獲得優惠
        foreach ($discounts_id as $key => $discount_id) {
            if ($order_total[$key] >= $sys_total[$key]) {
                $discount_flag .= $discount_id;
                if ($discount_method[$key] == 1) {
                    $other_sum[$key] =(string)($other_total - $order_total[$key]);
                    $base = round($order_total[$key] / $sys_total[$key]);
                    $order_discount[$key] = $base * $sys_discount[$key];
                    $amount[$key] = $order_total[$key] + (string)($other_total - $order_total[$key]);
                } else {
                    $other_sum[$key] =  (string)($other_total - $order_total[$key]);
                    $order_discount[$key] =  round($order_total[$key] * $sys_discount[$key] / $oneHundred);
                    $amount[$key] = $order_total[$key] - round($order_total[$key] * $sys_discount[$key] / $oneHundred) ;
                }
                $order_total[$key] = '優惠活動累計消費金額 $' . $order_total[$key];
            }
        }
        //清空未達累計金額陣列
        $offset = 0;
        foreach ($discounts_id as $key => $discount_id) {
            if ($order_total[$key - $offset] < $sys_total[$key]) {
                array_splice($order_total, $key - $offset, 1);
                array_splice($order_discount, $key - $offset, 1);
                array_splice($amount, $key - $offset, 1);
                array_splice($other_sum, $key - $offset, 1);
                array_splice($discount_method, $key - $offset, 1);
                array_splice($discounts_id, $key - $offset, 1);
                $offset++;
            }
        }
        array_unshift($sys_total, 0);
        array_unshift($sys_discount, 0);
        array_unshift($order_total, 0);
        array_unshift($order_discount, 0);
        array_unshift($amount, $other_total);
        array_unshift($other_sum, 0);
        array_unshift($discount_method, 0);
        array_unshift($discounts_id, 0);
        $use_discount_flag =0;
        $obj = new myOrder;
        //判斷是否有可使用優惠
        if ($discount_flag != '') {
            if($request->sel_id>0){
                $obj->id = $request->discount_id;
                $obj->sysMethod = $discount_method[$request->sel_id];
                $obj->sysTotal = $sys_total[$request->sel_id];
                $obj->sysDiscount = $sys_discount[$request->sel_id];
                $obj->orderDiscount = $order_discount[$request->sel_id];
            }else{
                $obj->id = null;
                $obj->sysMethod = null;
                $obj->sysTotal=null;
                $obj->sysDiscount=null;
                $obj->orderDiscount = 0;
            }
        } else {
            $obj->id = null;
            $obj->sysMethod = null;
            $obj->sysTotal=null;
            $obj->sysDiscount=null;
            $obj->orderDiscount = 0;
        }
        //----計算優惠結束----//
        if($use_coin==null){
            $use_coin='0';
        }
        if($use_coin>$amount[$request->sel_id]+$other_sum[$request->sel_id]){
            return response()->json(['wrong' => '購物金大於結帳金額']);
        }
        DB::transaction(function() use ($request,
            $id,
            $user_id,
            $use_coin,
            $addr,
            $obj,
            $discounts_id){
            Order::updateOrCreate(['id' => $request->id],
                [
                    'user_id' => $user_id,
                    'use_coin' => $use_coin,
                    'addr' => $addr,
                    'sysTotal' => $obj->sysTotal,
                    'sysDiscount' => $obj->sysDiscount,
                    'sysMethod'=> $obj->sysMethod,
                    'orderDiscount' => $obj->orderDiscount,
                ]);
                foreach ($request->productID as $key => $product_id) {
                    $product = Product::find($product_id);
                    $product->quantity -= $request->quantity[$key];
                    $product->quantitySold += $request->quantity[$key];
                    $product->save();
                    $discount_flag ='0';
                    //TODO 設定在優惠活動範圍內的產編
                    
                    $products_discounts = Products_Discounts::where('product_id', '=', $product_id)
                        ->where('discount_id',$discounts_id[$request->sel_id])
                        ->get();
                    if($products_discounts){
                        foreach ($products_discounts as $key2 => $row) {
                            if($row->product_id == $product_id){
                                $discount_flag='1';
                            }
                        }
                    }
                    OrderDetail::updateOrCreate(
                        [
                            'id' => $id,
                            'productID' => $product_id,
                        ],
                        [
                            'user_id' => $user_id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $request->quantity[$key],
                            'discount_flag' => $discount_flag,
                        ]);
                    
                   
                }
        });
      
        return response()->json(['success' => '送出訂單,本次預計使用$'.$use_coin.'購物金']);
    }
    public function calc(Request $request)
    {
        $wrong_id = null;
        $addr = $request->addr;
        $discount_method = [];
        $order_total = [];
        $order_discount = [];
        $other_sum = [];
        $discounts_id = [];
        $sys_total = [];
        $sys_discount = [];
        $other_total = 0;
        $amount = [];
        $user =  User::find($request->user()->id);
        foreach ($request->quantity as $key => $quantity) {
            if ($quantity <= 0) {
                $wrong_id .= $request->productID[$key] . ' ';
            }
        }
        if ($wrong_id !== null) {
            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量錯誤']);
        }

        //遍歷購買產品陣列
        foreach ($request->productID as $key => $product_id) {
            $products_discounts = Products_Discounts::where('product_id', '=', $product_id)->get();
            $product = Product::find($product_id);
            $other_total += $product->price * $request->quantity[$key];
            if ($products_discounts) {
                //遍歷優惠活動陣列
                foreach ($products_discounts as $key2 => $pd) {
                    $discount = Discount::find($pd->discount_id);
                    if($user->level<$discount->user_lv){
                        continue;
                    }
                    //新增優惠
                    if (!in_array($pd->discount_id, $discounts_id)) {
                        //紀錄優惠ID
                        array_push($discounts_id, $pd->discount_id);
                        $discount = Discount::find($pd->discount_id);
                        //紀錄優惠模式
                        array_push($discount_method, $discount->method);
                        array_push($sys_total, $discount->total);
                        array_push($sys_discount, $discount->discount);
                        //紀錄消費金額
                        array_push($order_total, $product->price * $request->quantity[$key]);
                        array_push($order_discount, 0);
                        array_push($amount, 0);
                        array_push($other_sum, 0);
                        // if($order_total>=$pd->total){
                        //     array_push($order_discount,$discount->discount);
                        // }
                    } else {
                        $idx = array_search($pd->discount_id, $discounts_id);
                        //加總優惠活動消費金額
                        $order_total[$idx] += $product->price * $request->quantity[$key];
                    }
                }
            }
        }
        $oneHundred = 100;
        $base = 0;
        $discount_flag = '';
        //計算累計消費金額與可獲得優惠
        foreach ($discounts_id as $key => $discount_id) {
            if ($order_total[$key] >= $sys_total[$key]) {
                $discount_flag .= $discount_id;
                if ($discount_method[$key] == 1) {
                    $other_sum[$key] = '其他商品消費金額:$' . (string)($other_total - $order_total[$key]);
                    $base = round($order_total[$key] / $sys_total[$key]);
                    $order_discount[$key] = '可獲得 $' . $base * $sys_discount[$key] . '購物金';
                    $amount[$key] += $order_total[$key] + (string)($other_total - $order_total[$key]);
                } else {
                    $other_sum[$key] = '其他商品消費金額:$' . (string)($other_total - $order_total[$key]);
                    $order_discount[$key] = '折扣後金額 $' . round($order_total[$key] * (1-$sys_discount[$key] / $oneHundred));
                    $amount[$key] += round($order_total[$key] * (1-$sys_discount[$key] / $oneHundred)) + (string)($other_total - $order_total[$key]);
                }
                $order_total[$key] = '優惠活動累計消費金額 $' . $order_total[$key];
            }
        }
        //清空未達累計金額陣列
        $offset = 0;
        foreach ($discounts_id as $key => $discount_id) {
            if ($order_total[$key - $offset] < $sys_total[$key]) {
                array_splice($order_total, $key - $offset, 1);
                array_splice($order_discount, $key - $offset, 1);
                array_splice($amount, $key - $offset, 1);
                array_splice($other_sum, $key - $offset, 1);
                $offset++;
            }
        }
        array_unshift($order_total, 0);
        array_unshift($order_discount, 0);
        array_unshift($amount, $other_total);
        array_unshift($other_sum, 0);
        //判斷是否有可使用優惠
        if ($discount_flag != '') {
            return response()->json(['success' => '可使用優惠',
                'id' => $discounts_id,
                'total' => $order_total,
                'discount' => $order_discount,
                'other' => $other_sum,
                'amount' => $amount,
                'coin' => $user->coin]);
        } else {
            return response()->json(['error' => '不符合優惠條件', 'amount' => $other_total,'coin' => $user->coin]);
        }

    }
    public function getOrder(Request $request)
    {
        if ($request->ajax()) {
            $user_id = $request->user()->id;
            //$data = Order::where('user_id',$user_id)->get();
            //DB::enableQueryLog(); // Enable query log

            // Your Eloquent query executed by using get()

            $order = DB::table('orders')
                ->join('orderDetails', 'orders.id', 'orderDetails.id')
                ->select(DB::raw('orders.* ,SUM(orderDetails.price * orderDetails.quantity ) as total'))
                ->where('orders.user_id',$user_id)
                ->groupBy('orders.id')
                ->orderBy('orders.id', 'desc')
                ->get();
            //$data2 = DB::select('SELECT p.productID,c.id,p.name,c.name as category FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID ,c.id,c.name');
            
            //dd(DB::getQueryLog()); // Show results of log
            return Datatables::of($order)
                ->addColumn('details', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="Details" class="btn btn-primary btn-sm details-control">明細</a>';
                    return $btn;
                })

                ->addColumn('action', function ($row) {
                    $actionText = "";

                    switch ($row->status) {
                        case '待出貨':
                            $actionText = "取消訂單";
                            break;
                        case '已出貨':
                            $actionText = "取貨";
                            break;
                        case '已付款取貨':
                            $actionText = "退貨";
                            break;
                        default:
                            return;
                            break;
                    }
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText . '" class="btn btn-danger btn-sm ' . $actionText . '">' . $actionText . '</a>';

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
            $user_id = $request->user()->id;
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


    public function cancelOrder(Request $request)
    {
        DB::transaction(function() use($request){
            $order = Order::find($request->id);
            $orderDetail = OrderDetail::where('id', $request->id)->get();
            
            $order->status = "訂單取消";
            $order->save();
            $order->delete();
            foreach ($orderDetail as $key => $row) {
                $row->status = "訂單取消";
                $row->delete();
                $product = Product::find($row->productID);
                $product->quantity += $row->quantity;
                $product->quantitySold -= $row->quantity;
                $row->save();
                $product->save();
            }
        });
        
        return response()->json(['success' => '訂單取消成功']);
    }
    public function receipt(Request $request)
    {
        $user=User::find($request->user()->id);
        $config = Config::find(1);
        $order = Order::find($request->id);
        $orderDetail = OrderDetail::where('id',$request->id)->get();
        $amount=0;
        $lv = null;
        $exp = null;
        
        foreach ($orderDetail as $key => $row) {
           $amount += $row->quantity * $row->price;
        }
        if($order->sysMethod==1){
            //計算經驗
            $exp = $user->exp_bar + $amount;
            $lv = round($exp/$config->moneyToLevel);
            if($lv>0 && $user->level < 10){
                if($lv - $user->level > $config->upgrade_limit){
                    $user->level += $config->upgrade_limit;
                    $exp = $config->moneyToLevel * $config->upgrade_limit;
                    $user->exp_bar += $exp;
                }else{
                    $exp = $amount;
                    $user->level = $lv;
                    $user->exp_bar += $exp;
                }
            }else{
                $exp ='等級已達上限';
            }
            if($order->use_coin!=null || $order->use_coin!='0'){
                $user->coin -= $order->use_coin;
            }
            $user->coin += $order->orderDiscount;
            DB::transaction(function() use($user,$order,$orderDetail){
                $user->save();
                $order->status = "已付款取貨";
                $order->save();
                foreach ($orderDetail as $key => $row) {
                    $row->status="已付款取貨";
                    $row->save();
                }
            });
            
            return response()->json(['success' => '您已成功付款取貨,本筆訂單獲得$'.$order->orderDiscount.'購物金,本次累積'.$exp.'經驗']);
        }else if($order->sysMethod==2){
            $amount -= $order->orderDiscount;
            //計算經驗
            $exp = $user->exp_bar + $amount;
            $lv = round($exp/$config->moneyToLevel);
            if($lv>0 && $user->level < 10){
                if($lv - $user->level > $config->upgrade_limit){
                    $user->level += $config->upgrade_limit;
                    $exp = $config->moneyToLevel * $config->upgrade_limit;
                    $user->exp_bar += $exp;
                }else{
                    $exp = $amount;
                    $user->level = $lv;
                    $user->exp_bar += $exp;
                }
            }else{
                $exp ='等級已達上限';
            }
            if($order->use_coin!=null || $order->use_coin!='0'){
                $user->coin -= $order->use_coin;
            }
            DB::transaction(function() use($user,$order,$orderDetail){
                $user->save();
                $user->save();
                $order->status = "已付款取貨";
                $order->save();
                foreach ($orderDetail as $key => $row) {
                    $row->status="已付款取貨";
                    $row->save();
                }
            });
            
            return response()->json(['success' => '您已成功付款取貨,本次累積'.$exp.'經驗']);
        }else {
            //計算經驗
            $exp = $user->exp_bar + $amount;
            $lv = round($exp/$config->moneyToLevel);
            if($lv>0 && $user->level < 10){
                if($lv - $user->level > $config->upgrade_limit){
                    $user->level += $config->upgrade_limit;
                    $exp = $config->moneyToLevel * $config->upgrade_limit;
                    $user->exp_bar += $exp;
                }else{
                    $exp = $amount;
                    $user->level = $lv;
                    $user->exp_bar += $exp;
                }
            }else{
                $exp ='等級已達上限';
            }
            if($order->use_coin!=null || $order->use_coin!='0'){
                $user->coin -= $order->use_coin;
            }
            DB::transaction(function() use($user,$order,$orderDetail){
                $user->save();
                $order->status = "已付款取貨";
                $order->save();
                foreach ($orderDetail as $key => $row) {
                    $row->status="已付款取貨";
                    $row->save();
                }
            });
            
            return response()->json(['success' => '您已成功付款取貨,本次累積'.$exp.'經驗']);
        }
        
        
        
       
    }
    public function returnOrder(Request $request)
    {
        DB::transaction(function() use($request){
            $order = Order::find($request->id);
            $orderDetail = OrderDetail::where('id',$request->id)->get();
            $order->status = "待退貨";
            $order->save();
            foreach ($orderDetail as $key => $row) {
                $row->status="待退貨";
                $row->save();
            }
        });
        
        return response()->json(['success' => '等待賣家同意退貨中']);
    }
    public function returnOrderDetail(Request $request)
    {
        DB::transaction(function() use($request){
            $order = Order::find($request->id);
            $orderDetail = DB::table('orderDetails')
                ->where('id','=',$request->id)
                ->where('productID','=',$request->product_id)
                ->update(['status' => "待退貨"]);
        
            $order->status = "部份商品待退貨";
            $order->save();
        });
       
        //DB::enableQueryLog(); // Enable query log
        //dd(DB::getQueryLog()); // Show results of log
        return response()->json(['success' => '等待賣家同意退貨中']);
    }
    // public function cancelOrderDetail(Request $request)
    // {
    //     $orderDetail = Order::find($request->id);
    //     $orderDetail->status = "訂單取消";
    //     $orderDetail->save();
    //     $orderDetail->delete();
    //     return response()->json(['success' => 'Order cancel successfully.']);
    // }

}
