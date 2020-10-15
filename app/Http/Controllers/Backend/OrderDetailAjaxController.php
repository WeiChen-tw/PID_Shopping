<?php

namespace App\Http\Controllers\Backend;

use App\Discount;
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

        foreach ($request->quantity as $key => $quantity) {
            if ($quantity <= 0) {
                $wrong_id .= $request->productID[$key] . ' ';
            }
        }
        if ($wrong_id !== null) {
            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量錯誤']);
        }
        if (!isset($addr)) {
            return response()->json(['wrong' => '請輸入地址']);
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
                    $base = floor($order_total[$key] / $sys_total[$key]);
                    $order_discount[$key] = $base * $sys_discount[$key];
                    $amount[$key] = $order_total[$key] + (string)($other_total - $order_total[$key]);
                } else {
                    $other_sum[$key] = '其他商品消費金額:$' . (string)($other_total - $order_total[$key]);
                    $order_discount[$key] =  floor($order_total[$key] * $sys_discount[$key] / $oneHundred);
                    $amount[$key] = floor($order_total[$key] * $sys_discount[$key] / $oneHundred) + (string)($other_total - $order_total[$key]);
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
                $offset++;
            }
        }
        array_unshift($order_total, 0);
        array_unshift($order_discount, 0);
        array_unshift($amount, $other_total);
        array_unshift($other_sum, 0);
        array_unshift($discount_method, 0);
        $use_discount_flag =0;
        $obj = new myDiscount;
        //判斷是否有可使用優惠
        if ($discount_flag != '') {
            if($request->sel_id>0){
                $use_discount_flag=1;
                $discount = Discount::find($request->discount_id);
                $obj->id = $request->discount_id;
                $obj->sysMethod = $discount_method[$request->sel_id];
                $obj->sysTotal = $discount->total;
                $obj->sysDiscount = $discount->discount;
                $obj->orderDiscount = $order_discount[$request->sel_id];
            }else{
                $obj->id = null;
                $obj->sysMethod = null;
                $obj->sysTotal=null;
                $obj->sysDiscount=null;
                $obj->orderDiscount = null;
            }
        } else {
            $obj->id = null;
            $obj->sysMethod = null;
            $obj->sysTotal=null;
            $obj->sysDiscount=null;
            $obj->orderDiscount = null;
        }
        //----計算優惠結束----//
        Order::updateOrCreate(['id' => $request->id],
            [
                'user_id' => $user_id,
                'addr' => $addr,
                'sysTotal' => $obj->sysTotal,
                'sysDiscount' => $obj->sysDiscount,
                'sysMethod'=> $obj->sysMethod,
                'orderDiscount' => $obj->orderDiscount,
            ]);
        foreach ($request->productID as $key => $product_id) {
            $product = Product::find($product_id);
            OrderDetail::updateOrCreate(
                [
                    'id' => $id,
                    'productID' => $product_id,
                ],
                [
                    'user_id' => $user_id,
                    'price' => $product->price,
                    'quantity' => $request->quantity[$key],
                    //'discount' => '',
                ]);
        }
        // if($obj->sysMethod==1){
        //     $user=User::find($user_id);
        //     $user->coin += $obj->orderDiscount;
        //     $user->save();
        // }
        

        return response()->json(['success' => '送出訂單']);
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
                    $base = floor($order_total[$key] / $sys_total[$key]);
                    $order_discount[$key] = '可獲得 $' . $base * $sys_discount[$key] . '購物金';
                    $amount[$key] += $order_total[$key] + (string)($other_total - $order_total[$key]);
                } else {
                    $other_sum[$key] = '其他商品消費金額:$' . (string)($other_total - $order_total[$key]);
                    $order_discount[$key] = '折扣後金額 $' . floor($order_total[$key] * (1-$sys_discount[$key] / $oneHundred));
                    $amount[$key] += floor($order_total[$key] * (1-$sys_discount[$key] / $oneHundred)) + (string)($other_total - $order_total[$key]);
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
                'amount' => $amount]);
        } else {
            return response()->json(['error' => '不符合優惠條件', 'amount' => $other_total]);
        }

    }
    public function getOrder(Request $request)
    {
        if ($request->ajax()) {
            //$user_id = $request->user()->id;
            $data = Order::get();
            //DB::enableQueryLog(); // Enable query log

            // Your Eloquent query executed by using get()

            $order = DB::table('orders')
                ->join('orderDetails', 'orders.id', 'orderDetails.id')
                ->join('users', 'orders.user_id', 'users.id')
                ->select('orders.id', 'orders.user_id','users.name','orders.created_at', 'orders.status', DB::raw('SUM(orderDetails.price * orderDetails.quantity - orders.orderDiscount) as total'))
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
                            $actionText = "出貨";
                            break;
                        case '退貨中':
                            $actionText = "同意退貨";
                            break;
                        default:
                            return;
                            break;
                    }
                    $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText . '" class="btn btn-danger btn-sm ' . $actionText . '">' . $actionText . '</a>';
                    if($actionText === "同意退貨"){
                        $actionText2 = "拒絕退貨";
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText2 . '" class="btn btn-success btn-sm ' . $actionText2 . '">' . $actionText2 . '</a>';
                    
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
                ->join('products', 'orderDetails.productID', 'products.productID')
                ->where('orderDetails.id', $id)
                ->select('orderDetails.*', 'products.name', DB::raw('SUM(orderDetails.price * orderDetails.quantity) as total'))
                ->groupBy('orderDetails.id',
                    'orderDetails.user_id',
                    'orderDetails.status',
                    'orderDetails.quantity',
                    'orderDetails.price',
                    'orderDetails.discount',
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

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\ShopCart  $product

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        //$product = ShopCart::find($id);
        //$product = ShopCart::where('productID', $id)->first();
        //$product->save();
        return response()->json($product);
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\ShopCart  $product

     * @return \Illuminate\Http\Response

     */

    public function ship(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = "已出貨";
        $order->save();
        return response()->json(['success' => '出貨成功']);
    }
    public function returnOrder(Request $request)
    {
        if($request->action=='yes'){

            $order = Order::find($request->id);
            
            if($order->sysMethod==1){
                $user=User::find($order->user_id);
                $user->coin -= $order->orderDiscount;
                $user->save();
                $order->status = "退貨成功";
                $order->save();
                return response()->json(['success' => '退貨成功,回收$'.$order->orderDiscount.'購物金']);
            }else if($order->sysMethod==2){
                $order->status = "退貨成功";
                $order->save();
                return response()->json(['success' => '退貨成功']);
            }else{
                $order->status = "退貨成功";
                $order->save();
                return response()->json(['success' => '退貨成功']);
            }
            //$order->delete();
            return response()->json(['success' => '退貨成功']);
        }else if($request->action=='no'){
            $order = Order::find($request->id);
            $order->status = "退貨失敗";
            $order->save();
            //$order->delete();
            return response()->json(['success' => '拒絕退貨成功']);
        }
        
    }
}
