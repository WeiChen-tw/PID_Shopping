<?php

namespace App\Http\Controllers\Frontend;

use App\Product;
use App\ShopCart;
use App\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        return ;
        if ($request->ajax()) {
            $data = DB::table('shopCart')
                ->join('products', 'shopCart.productID', '=', 'products.productID')
                ->select('shopCart.id',
                    'shopCart.productID',
                    'products.name',
                    'products.price',
                    'shopCart.quantity',
                    'products.img')
                ->get();
            foreach ($data as $key => $value) {
                //echo $value->name;
                $value->img = base64_encode($value->img);
            }
            return response()->json($data);
        }
    }

    
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {
        $max_id=DB::select('select max(id) as id from `orders`');
        $id = $max_id[0]->id+1;
        $user_id = $request->user()->id;

        foreach ($request->productID as $key => $product_id) {
            $product = Product::find($product_id);
            Order::create(
            [
                'id' => $id, 
                'user_id' => $user_id,
                'productID' => $product_id,
                'price' => $product->price,
                'quantity' => $request->quantity[$key],
                //'discount' => '',
            ]);
        }
        
        return response()->json(['success' => '送出訂單']);
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

    public function destroy($id,Request $request)
    {
        
        ShopCart::where('user_id','=',$request->user()->id)
            ->where('id','=',$id)
            ->delete();

        return response()->json(['success' => 'ShopCart deleted successfully.']);

    }

}
