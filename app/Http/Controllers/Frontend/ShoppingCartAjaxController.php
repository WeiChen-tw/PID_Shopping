<?php

namespace App\Http\Controllers\Frontend;

use App\Product;
use App\ShopCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingCartAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

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

    public function getProductData(Request $request)
    {

        if ($request->table == 'category' && isset($request->id)) {
            $data = DB::table('products')
                ->join('products_categories', 'products.productID', '=', 'products_categories.product_id')
                ->where('products_categories.category_id', '=', $request->id)
                ->get();
        } else if ($request->table == 'discount' && isset($request->id)) {
            $data = DB::table('products')
                ->join('products_discounts', 'products.productID', '=', 'products_discounts.product_id')
                ->where('products_discounts.discount_id', '=', $request->id)
                ->get();
        } else {
            $data = ShopCart::all();
        }

        foreach ($data as $key => $value) {
            //echo $value->name;
            $value->img = base64_encode($value->img);
        }
        return response()->json($data);
    }
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {
        if(!Auth::check()){
            return ;
        }
        $user_id = $request->user()->id;
        $product_id = $request->id;
        $product = Product::find($product_id);
        ShopCart::updateOrCreate(['user_id' => $user_id, 'productID' => $product_id],
            [

                'price' => $product->price,
                'quantity' => $request->quantity,
            ]);
        return response()->json(['success' => 'ShopCart saved successfully.']);
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

    public function destroy($idArr,Request $request)
    {
        //$id = product_id
        $idArr = (explode(",", $idArr));
        foreach ($idArr as $key => $id) {
            ShopCart::where('user_id','=',$request->user()->id)
            ->where('productID','=',$id)
            ->delete();
        }

        return response()->json(['success' => 'ShopCart deleted successfully.']);

    }

}
