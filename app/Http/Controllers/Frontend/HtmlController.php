<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HtmlController extends Controller
{
    public function loadProduct(Request $request)
    {
        $products = DB::table('products')
            ->leftJoin('products_discounts','products.productID','products_discounts.product_id')
            ->leftJoin('discounts','products_discounts.discount_id','discounts.id')
            ->where('products.onMarket','Y')
            ->select(DB::raw('products.*,GROUP_CONCAT(DISTINCT discounts.name) as discountName'))
            ->groupBy('products.productID')
            ->get();
        
        if (!$products) {
            return response()->json(array('success' => false, 'html' => 'No Product'));
        }else {
            $returnHtml = view('frontend.showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
    }
    public function searchKeyword(Request $request)
    {
        $keyword = $request->keyword;
        //$keyword = 'p1';
        
        $products = DB::table('products')
            ->leftJoin('products_discounts','products.productID','products_discounts.product_id')
            ->leftJoin('discounts','products_discounts.discount_id','discounts.id')
            ->where('products.onMarket','Y')
            ->where('products.name','like','%'.$keyword.'%')
            ->select(DB::raw('products.*,GROUP_CONCAT(DISTINCT discounts.name) as discountName'))
            ->groupBy('products.productID')
            ->get();
        if (!$products) {
            return response()->json(array('success' => false, 'html' => '查無商品'));
        }else {
            $returnHtml = view('frontend.showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
    }
    public function searchCategory(Request $request)
    {
        $keyword = $request->keyword;
        $keyword = 'p1';
        $products = DB::select('select * from products where onMarket="Y"');
        $products = DB::table('products')
            ->join('products_categories','products.productID','products_categories.product_id')
            ->leftJoin('products_discounts','products.productID','products_discounts.product_id')
            ->leftJoin('discounts','products_discounts.discount_id','discounts.id')
            ->where('products.onMarket','Y')
            ->where('category_id',$request->category_id)
            ->select(DB::raw('products.*,GROUP_CONCAT(DISTINCT discounts.name) as discountName'))
            ->groupBy('products.productID')
            ->get();
        if (!$products) {
            return response()->json(array('success' => false, 'html' => '查無商品'));
        }else {
            $returnHtml = view('frontend.showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
    }
    public function orderBy(Request $request)
    {
        $name = array('price'=>'','updated_at' => '');
        $name[$request->sortBy] = $request->sortBy;
    
        if($request->category_id==''){
            $products = DB::table('products')
            ->leftJoin('products_discounts','products.productID','products_discounts.product_id')
            ->leftJoin('discounts','products_discounts.discount_id','discounts.id')
            ->where('products.onMarket','Y')
            ->when($name['price'],function($q) use ($name){
                return $q->orderBy($name['price']);
            })
            ->when($name['updated_at'],function($q) use ($name){
                return $q->orderBy($name['updated_at'],'desc');
            })
            ->select(DB::raw('products.*,GROUP_CONCAT(DISTINCT discounts.name) as discountName'))
            ->groupBy('products.productID')
            ->get();
        }else if(isset($request->category_id)){
            $products = DB::table('products')
            ->join('products_categories','products.productID','products_categories.product_id')
            ->leftJoin('products_discounts','products.productID','products_discounts.product_id')
            ->leftJoin('discounts','products_discounts.discount_id','discounts.id')
            ->where('onMarket','Y')
            ->where('category_id',$request->category_id)
            ->when($name['price'],function($q) use ($name){
                return $q->orderBy($name['price']);
            })
            ->when($name['updated_at'],function($q) use ($name){
                return $q->orderBy($name['updated_at'],'desc');
            })
            ->select(DB::raw('products.*,GROUP_CONCAT(DISTINCT discounts.name) as discountName'))
            ->groupBy('products.productID')
            ->get();
        }
        if (!$products) {
            return response()->json(array('success' => false, 'html' => '查無商品'));
        }else {
            $returnHtml = view('frontend.showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
    }
   public function getCategory()
   {
       $categories = DB::table('categories')->get();
       if($categories)
       {
        return response()->json(array('success' => $categories));
       }
       else 
       {
        return response()->json(array('error' => '查詢錯誤分類'));
       }
   }
        
        
        
}
