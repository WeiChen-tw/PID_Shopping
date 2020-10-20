<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HtmlController extends Controller
{
    public function loadProduct(Request $request)
    {
        $products = DB::table('products')
            ->where('onMarket','Y')
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
            ->where('onMarket','Y')
            ->where('name','like','%'.$keyword.'%')
            ->get();
        if (!$products) {
            return response()->json(array('success' => false, 'html' => 'No Product'));
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
            ->where('onMarket','Y')
            ->where('category_id',$request->category_id)
            ->select(DB::raw('products.*'))
            ->get();
        if (!$products) {
            return response()->json(array('success' => false, 'html' => 'No Product'));
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
