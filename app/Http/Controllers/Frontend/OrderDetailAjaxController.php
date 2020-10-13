<?php

namespace App\Http\Controllers\Frontend;

use App\Order;
use App\OrderDetail;
use App\Product;
use App\ShopCart;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        foreach ($request->quantity as $key => $quantity) {

            if ($quantity <= 0) {
                $wrong_id .= $request->productID[$key] . ' ';
            }

        }
        if ($wrong_id !== null) {
            return response()->json(['wrong' => '商品編號:' . $wrong_id . ' 數量錯誤']);
        }
        Order::updateOrCreate(['id' => $request->id],
            [
                'user_id' => $user_id,
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

        return response()->json(['success' => '送出訂單']);
    }

    public function getOrder(Request $request)
    {
        if ($request->ajax()) {
            $user_id = $request->user()->id;
            $data = Order::get();
            //DB::enableQueryLog(); // Enable query log

            // Your Eloquent query executed by using get()

            $order = DB::table('orders')
                ->join('orderDetails', 'orders.id', 'orderDetails.id')
                ->select('orders.id', 'orders.created_at', 'orders.status', DB::raw('SUM(orderDetails.price * orderDetails.quantity) as total'))
                ->groupBy('orders.id')
                ->orderBy('orders.id', 'desc')
                ->get();
            //$data2 = DB::select('SELECT p.productID,c.id,p.name,c.name as category FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID ,c.id,c.name');
            //dd(DB::getQueryLog()); // Show results of log
            return Datatables::of($order)
                ->addColumn('details', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="order" data-id="' . $row->id . '" data-original-title="Details" class="btn btn-primary btn-sm details-control">Details</a>';
                    return $btn;
                })

                ->addColumn('action', function ($row) {
                    $actionText = "ttt";
                    
                    switch ($row->status) {
                        case '待出貨':
                            $actionText = "cancelOrder";
                            break;
                        case '已收貨':
                            $actionText = "return";
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
        $product = ShopCart::where('productID', $id)->first();
        //$product->save();
        return response()->json($product);
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\ShopCart  $product

     * @return \Illuminate\Http\Response

     */

    public function cancelOrder(Request $request)
    {
        $order = Order::find($request->id);
        $order->status = "訂單取消";
        $order->save();
        $order->delete();
        return response()->json(['success' => 'Order cancel successfully.']);
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
