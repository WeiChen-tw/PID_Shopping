<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HtmlController extends Controller
{
    public function loadProduct()
    {
        $products = DB::select('select * from products where onMarket="Y"');
        
        if (!$products) {
            return response()->json(array('success' => false, 'html' => 'No Product'));
        }else {
            $returnHtml = view('backend.showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
        
    }
        
        
        
}
