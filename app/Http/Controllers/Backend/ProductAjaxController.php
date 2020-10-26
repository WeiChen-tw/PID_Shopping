<?php

namespace App\Http\Controllers\Backend;

use App\Product;
use App\Products_Categories;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;
class ProductAjaxController extends Controller
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
       
        if ($request->ajax()) {
            $data = Product::latest()->get();

            $sql = <<<STR
            SELECT p.productID,p.name,GROUP_CONCAT(DISTINCT c.name) as category,GROUP_CONCAT(DISTINCT d.method)as discount,p.quantity,p.quantitySold ,p.price,p.description,p.img,p.onMarket 
            FROM `products` as p 
            INNER JOIN products_categories as pc 
            LEFT JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id 
            INNER JOIN products_discounts as pd 
            LEFT JOIN discounts as d on p.productID = pd.product_id and pd.discount_id = d.id 
            GROUP BY p.productID
STR;
            $data2 = DB::select($sql);
            return Datatables::of($data2)
            //->addIndexColumn()

                ->addColumn('check', function ($row) {
                    $check = '<input type="checkbox" data-id="' . $row->productID . '">';
                    return $check;
                })
                ->addIndexColumn()

                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="products" data-id="' . $row->productID . '" data-original-title="Edit" class="edit btn btn-primary btn-sm edit">編輯</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="products" data-id="' . $row->productID . '" data-original-title="Delete" class="btn btn-danger btn-sm delete">刪除</a>';

                    return $btn;

                })
                ->editColumn('img', function ($row) {
                    if ($img = $row->img) {
                        //return  '<img class="img-fluid" src="data:image/jpeg;base64,'. base64_encode($img). '">';
                        return sprintf(
                            '<img class="img-fluid" src="data:image/jpeg;base64,%s">',
                            base64_encode($img)
                        );
                    }
                    return ' ';
                })
                ->rawColumns(['img', 'action', 'check'])
                ->make(true);

        }

        return view('backend.home', compact('products'));

    }

    public function getProductData(Request $request)
    {
        $data=null;
        if($request->action=='add'){
            //$data = Product::all();
            
            DB::enableQueryLog(); // Enable query log
            if($request->table == 'category'){
                
                $data = DB::select('select products.* from products   left join products_categories on products.productID = products_categories.product_id  where products_categories.product_id not in (SELECT product_id FROM products_categories WHERE category_id = '.$request->id.') or products_categories.product_id IS NULL GROUP BY products.productID');
                // $data = DB::table('products')
                // ->leftJoin('products_categories', 'products.productID', '=', 'products_categories.product_id')
                // ->where('products_categories.category_id', '<>', $request->id)
                // ->groupBY('products.productID')
                // ->select(DB::raw('products.*'))
                // ->get();
            }else if($request->table == 'discount'){
                $data = DB::select('select products.* from products   left join products_discounts on products.productID = products_discounts.product_id  where products_discounts.product_id not in (SELECT product_id FROM products_discounts WHERE discount_id ='.$request->id.') or products_discounts.product_id IS NULL GROUP BY products.productID');
                // $data = DB::table('products')
                // ->join('products_discounts', 'products.productID', '=', 'products_discounts.product_id')
                // ->where('products_discounts.discount_id', '<>', $request->id)
                // ->get();
            }
            // dd(DB::getQueryLog()); // Show results of log
        }
        else if ($request->table == 'category' && isset($request->id)) {
            $data = DB::table('products')
                ->join('products_categories', 'products.productID', '=', 'products_categories.product_id')
                ->where('products_categories.category_id', '=', $request->id)
                ->get();
        } else if ($request->table == 'discount' && isset($request->id)) {
            $data = DB::table('products')
                ->join('products_discounts', 'products.productID', '=', 'products_discounts.product_id')
                ->where('products_discounts.discount_id', '=', $request->id)
                ->get();
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
        
        $image=null;
        if($request->hasFile('file')){
            $file = $request->file('file');
        
          
            $originFilename = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $file_mime = $file->getClientOriginalExtension();
            $fileName = uniqid() . '.' .
            $file->getClientOriginalExtension();
            $path = $file->getRealPath();
            $image = file_get_contents($path);
        }
        
        $new_category=[];
        if($request->category){
            $new_category = explode(",", $request->category);
        }
        if($image){
            $product = Product::updateOrCreate(['productID' => $request->product_id],
            ['id' => 1,
                'name' => $request->name,
                'img' => $image,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'quantitySold' => $request->quantitySold,
                'description' => $request->description]);
        }else{
            $product = Product::updateOrCreate(['productID' => $request->product_id],
            ['id' => 1,
                'name' => $request->name,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'quantitySold' => $request->quantitySold,
                'description' => $request->description]);
        }
        
        $arr = Products_Categories::where('product_id', $product->productID)->get();
        $old_category = [];
        $add_arr = [];
        $remove_arr = [];
        if (!empty($arr)) {

            foreach ($arr as $key => $row) {
                array_push($old_category, $row->category_id);
            }

            $add_arr = array_diff_assoc($new_category, $old_category);
            $remove_arr = array_diff_assoc($old_category, $new_category);
        } else {
            $add_arr = $new_category;
        }

        if (count($add_arr)) {
            foreach ($add_arr as $key => $category_id) {
                Products_Categories::updateOrCreate(
                    ['product_id' => $product->productID, 'category_id' => $category_id]
                );
            }
        }
        if (count($remove_arr)) {
            foreach ($remove_arr as $key => $category_id) {
                Products_Categories::where('product_id', $request->product_id)
                    ->where('category_id', $category_id)
                    ->delete();
            }
        }

        return response()->json(['success' => '成功儲存商品.']);
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Product  $product

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        //$product = Product::find($id);
        $product = DB::table('products')->where('productID', $id)
            ->leftJoin('products_categories','products.productID','products_categories.product_id')
            ->select('products.*',DB::raw('GROUP_CONCAT(DISTINCT products_categories.category_id) as category'))
            ->groupBy('products.productID')
            ->first();
        
        $product->img = 'data:image/jpeg;base64,'.base64_encode($product->img);
        
        
        //$product->save();
        return response()->json($product);
    }

    /**
     * on the market or take off
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function onOrOff(Request $request)
    {
        foreach ($request->id as $key => $id) {
            $product = Product::where('productID', $id)->first();
            $product->onMarket = $request->action;
            $product->save();
        }

        return response()->json(['success' => '操作成功.']);
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Product  $product

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        Product::find($id)->delete();

        return response()->json(['success' => '成功刪除商品.']);

    }

}
