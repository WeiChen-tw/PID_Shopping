<?php

namespace App\Http\Controllers\Backend;

use App\Models\Config;
use App\Order;
use App\OrderDetail;
use App\Product;
use App\Record;
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
class myRefund
{
    public $Money =array(
        refund => '',
        other => '',
    );
    
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
            //$data = Order::get();
            $order = DB::table('orders')
                ->join('orderDetails', 'orders.id', 'orderDetails.id')
                ->join('users', 'orders.user_id', 'users.id')
                ->select('users.name', DB::raw('orders.* ,SUM(orderDetails.price * orderDetails.quantity) as total'))
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
        DB::transaction(function () use ($request) {
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
            } else {
                $exp = $amount;
            }
            if ($order->sysMethod == 1) {
                $user->exp_bar -= $exp;
                $lv = floor($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    if ($lv - $user->level <= $config->upgrade_limit) {
                        $user->level -= $config->upgrade_limit;
                    } else {
                        $user->level = $lv;
                    }
                } else {
                    $exp = '等級已達上限';
                }
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '-' . $order->orderDiscount));
                $user->coin -= $order->orderDiscount;
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($user, $order, $orderDetail) {
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

                return response()->json(['success' => '退貨成功,系統收回$' . $order->orderDiscount . '購物金與經驗值' . $exp]);
            } else if ($order->sysMethod == 2) {
                $user->exp_bar -= $exp;
                $lv = floor($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($user, $order, $orderDetail) {
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

                return response()->json(['success' => '退貨成功,系統收回經驗值' . $exp]);
            } else {
                $user->exp_bar -= $exp;
                $lv = floor($user->exp_bar / $config->moneyToLevel);
                if ($lv >= 0 && $user->level < 10) {
                    $user->level = $lv;
                }
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($user, $order, $orderDetail) {
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

                return response()->json(['success' => '退貨成功,系統收回經驗值' . $exp]);
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
            $orderTotal = array(0, 0);
            $user = User::find($order->user_id);
            $product_id = null;
            $amount = 0;
            $discountCoin =0;
            $exp = null;
            $sum = null;
            $msgFlag=null;
            $this_item = DB::table('orderDetails')
                ->where('id', '=', $request->id)
                ->where('productID', '=', $request->product_id)
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
                        if ($row->discount_flag == 1) {
                            $orderTotal[0] += $amount;
                        }
                    } else {
                        $sum += $row->quantity * $row->price;
                        $orderTotal[1] += $sum;
                    }
                }
            }
            $sum += $amount;
            $orderTotal[1] += $amount;
            //優惠模式2 要計算折扣數
            if ($order->sysMethod == 2) {
                //計算折扣數
                if ($this_item->discount_flag == 1) {
                    $amount = round($amount * (1 - $order->sysDiscount / 100));
                }
            }
            //設定經驗為可接受最大值
            //計算當筆訂單總額
            // 回朔等級 重算訂單經驗值
            if ($sum > $exp_max) {
                
                $exp = $exp_max;
            } else {
                //優惠模式2 要計算折扣數
                 if ($order->sysMethod == 2) {
                    //計算折扣數
                    if ($this_item->discount_flag == 1) {
                        $exp = round($sum * (1 - $order->sysDiscount / 100));
                    }
                }else{
                    $exp = $sum;
                }
                
            }
            //剩餘訂單金額大於訂單經驗上限
            if (($sum -= $amount) > $exp_max) {
                $sum = $exp_max;
                $msgFlag =1;
            }
            //判斷優惠模式 1 購物金 2 折扣
            if ($order->sysMethod == 1) {
                $user->exp_bar -= $exp;
                $lv = floor(($user->exp_bar+ $sum) / $config->moneyToLevel) ;
                if($msgFlag!=1){
                    if ($lv >= 0 && $user->level < 10) {
                        if ($lv - $user->level <= $config->upgrade_limit) {
                            $user->level -= $config->upgrade_limit;
                        } else {
                            $user->level = $lv;
                        }
                    } else {
                        $sum = '等級已達上限';
                    }
                }
               
                if (($orderTotal[1] - $orderTotal[0]) >= $order->sysTotal) {
                    $base = floor(($orderTotal[1] - $orderTotal[0]) / $order->sysTotal);
                    $discountCoin = $order->orderDiscount - ($base * $order->sysDiscount);
                    Record::create(array('user_id' => $order->user_id,
                        'field' => 'coin',
                        'src' => $user->coin,
                        'status' => '-' . $discountCoin));
                    $user->coin -= $discountCoin;
                }
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($request, $user, $order, $this_item) {
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();

                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                        ->where('id', '=', $request->id)
                        ->where('productID', '=', $request->product_id)
                        ->update(['status' => "退貨成功"]);
                });
                 
                if($msgFlag==null){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount - $discountCoin. '購物金與回收獲得經驗值' . $exp]);
                }else if($msgFlag==1){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount - $discountCoin. '購物金,維持經驗值']);
                }
                
            } else if ($order->sysMethod == 2) {
                $user->exp_bar -= $exp;
                
                $lv = floor(($user->exp_bar+ $sum) / $config->moneyToLevel) ;
                if($msgFlag!=1){
                    if ($lv >= 0 && $user->level < 10) {
                        $user->level = $lv;
                    } else {
                        $sum = '等級已達上限';
                    }
                }
                
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($request, $user, $order, $this_item) {
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();

                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                        ->where('id', '=', $request->id)
                        ->where('productID', '=', $request->product_id)
                        ->update(['status' => "退貨成功"]);

                });

               if($msgFlag==null){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount . '購物金與回收獲得經驗值' . $exp]);
                }else if($msgFlag==1){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount . '購物金,維持經驗值']);
                }
            } else {
                $user->exp_bar -= $exp;
                $lv = floor(($user->exp_bar+ $sum) / $config->moneyToLevel) ;
                if($msgFlag!=1){
                    if ($lv >= 0 && $user->level < 10) {
                        $user->level = $lv;
                    } else {
                        $sum = '等級已達上限';
                    }
                }
                
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '+' . $amount));
                $user->coin += $amount;
                DB::transaction(function () use ($request, $user, $order, $this_item) {
                    $user->save();
                    $order->status = "部份商品退貨成功";
                    $order->save();

                    $product = Product::find($this_item->productID);
                    $product->quantity += $this_item->quantity;
                    $product->quantitySold -= $this_item->quantity;
                    $product->save();
                    DB::table('orderDetails')
                        ->where('id', '=', $request->id)
                        ->where('productID', '=', $request->product_id)
                        ->update(['status' => "退貨成功"]);

                });
                if($msgFlag==null){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount . '購物金與回收獲得經驗值' . $exp]);
                }else if($msgFlag==1){
                    return response()->json(['success' => '退貨成功,系統退回$' . $amount . '購物金,維持經驗值']);
                }
                
            }
            return response()->json(['success' => '退貨成功']);
        } else if ($request->action == 'no') {
            DB::transaction(function () use ($order, $orderDetail) {
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
    public function refund ($info , $obj,$orderDetail,$user )
    {
        if($info->method==1){

        }else if($info->method==2){

        }else{

        }
    }
}
