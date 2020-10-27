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
    public $money = array(
        'refund' => null,
        'other' => null,
        'sum' => null,
        'coinDiscountSum' => null,
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
                        case '部份商品待退貨':
                            $actionText = "請點選明細確認退貨";
                            break;
                        default:
                            return;
                            break;
                    }
                    if($actionText !== '請點選明細確認退貨'){
                        $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="' . $actionText . '" class="btn btn-danger btn-sm ' . $actionText . '">' . $actionText . '</a>';
                    }else{
                        $btn = $actionText;
                    }
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
            $discountCoin = 0;
            $exp = null;
            $sum = null;
            $coinDiscountSum = null;
            $msgFlag = null;
            $coinMsg = null;
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
                if ($product_id && $row->status != '退貨成功') {
                    //計算退貨商品金額
                    //優惠模式2 要計算折扣
                    if ($row->productID == $product_id) {
                        if ($order->sysMethod == 2 && $row->discount_flag == 1) {
                            $amount += round($row->quantity * $row->price * (1 - $order->sysDiscount / 100));
                        } else {
                            $amount += $row->quantity * $row->price;
                        }
                    } else {
                        //計算非退貨商品金額
                        //優惠活動內商品
                        if ($row->discount_flag == 1) {
                            //計算活動累計金額之後判斷是否達標
                            if ($order->sysMethod == 1) {
                                $coinDiscountSum += $row->quantity * $row->price;
                            } else if ($order->sysMethod == 2) {
                                //計算折扣
                                $sum += round($row->quantity * $row->price * (1 - $order->sysDiscount / 100));
                            }
                        } else {
                            //無優惠商品
                            $sum += $row->quantity * $row->price;

                        }
                    }
                }
            }

            $item = new myRefund;
            $item->money['refund'] = $amount;
            $item->money['other'] = $sum;
            $item->money['coinDiscountSum'] = $coinDiscountSum;
            $item->money['sum'] = $item->money['refund'] + $item->money['other'];
            $expMsg = $this->refundExp($user, $item);
           
            //判斷優惠模式
            // 1 購物金 2 折扣
            if ($order->sysMethod == 1) {
                //計算退貨購物金
                $coinMsg = $this->refundCoin($user, $order, $item);
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
                $amount -= $discountCoin;
                return response()->json(['success' => '退貨成功,' . $coinMsg . ',' . $expMsg]);

            } else if ($order->sysMethod == 2) {
                //計算退貨購物金
                $coinMsg = $this->refundCoin($user, $order, $item);
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
                return response()->json(['success' => '退貨成功,' . $coinMsg . ',' . $expMsg]);
            } else {
                //計算退貨購物金
                $coinMsg = $this->refundCoin($user, $order, $item);

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
                return response()->json(['success' => '退貨成功,' . $coinMsg . ',' . $expMsg]);
            }

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

    public function refundItem($info, $obj, $orderDetail, $user)
    {
        if ($info->method == 1) {

        } else if ($info->method == 2) {

        } else {

        }
    }

    public function refundExp($user, $item)
    {
        //$user = User::find($user_id);
        $config = Config::find(1);
        $exp_max = $config->moneyToLevel * $config->upgrade_limit;
        $old_exp = null;
        $msg = null;
        //重新計算訂單經驗
        //計算該訂單原本經驗
        if ($item->money['sum'] > $exp_max) {
            $old_exp = $exp_max;
        } else {
            $old_exp = $item->money['sum'];
        }
        //回溯經驗
        $user->exp_bar -= $old_exp;
        //重新計算訂單經驗
        //判斷剩餘訂單經驗是否超出上限
        if ($item->money['other'] > $exp_max) {
            $user->exp_bar += $exp_max;
        } else {
            $user->exp_bar += $item->money['other'];
        }
        $lv = floor($user->exp_bar / $config->moneyToLevel);

        if ($lv >= 0 && $user->level < 10) {
            $user->level = $lv;
            $msg = "重新計算後等級" . $user->level . ",經驗值" . $user->exp_bar;
        } else {
            $msg = '等級已達上限';
        }
        if ($user->level < 0 || $user->exp_bar < 0) {
            $msg = '計算錯誤';
        }
        return $msg;

    }
    public function refundCoin($user, $order, $item)
    {
        $coinDiff = null;
        $msg = null;
        $flag = 0;

        //計算購物金優惠活動是否成立與贈送金額
        if ($order->sysMethod == 1 && $order->active !='-1') {
            $flag = 1;
            //活動成立,扣除贈送購物金差額
            if ($item->money['coinDiscountSum'] >= $order->sysTotal) {
                $base = floor($item->money['coinDiscountSum'] / $order->sysTotal);
                $discountCoin = $order->orderDiscount - ($base * $order->sysDiscount);
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '-' . $discountCoin));
                $user->coin -= $discountCoin;
                $coinDiff = $discountCoin;
            } else {
                //活動不成立,回收贈送的全額購物金
                //將優惠觸發改為無觸發
                // $order->sysMethod = null;
                // $order->sysTotal = null;
                // $order->sysDiscount = null;
                // $order->orderDiscount ='0';
                $order->active ='-1';
                Record::create(array('user_id' => $order->user_id,
                    'field' => 'coin',
                    'src' => $user->coin,
                    'status' => '-' . $order->orderDiscount));
                $user->coin -= $order->orderDiscount;
                $coinDiff = $order->orderDiscount;
            }
        }
        //退貨金額轉購物金
        Record::create(array('user_id' => $order->user_id,
            'field' => 'coin',
            'src' => $user->coin,
            'status' => '+' . $item->money['refund']));
        $user->coin += $item->money['refund'];
        if ($flag == 0) {
            $msg = '系統退還$' . $item->money['refund'] . '購物金';
        } else if ($flag == 1) {
            $msg = '系統退還$' . $item->money['refund'] . '購物金,回收贈送購物金$' . $coinDiff;
        }
        return $msg;

    }
}
