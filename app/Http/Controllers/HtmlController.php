<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HtmlController extends Controller
{
    public function loadProduct()
    {
        $count =5;
        $products = DB::select('select * from product');
        
        if (!$products) {
            return response()->json(array('success' => false, 'html' => 'No Product'));
        }else {
            $returnHtml = view('showbox')->with('products',$products)->render();
            return response()->json(array('success' => true, 'html' => $returnHtml));
        }
    }
        
        
        
}
