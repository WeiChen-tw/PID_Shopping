<?php

namespace App\Http\Controllers\Backend;

use App\Product;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Product::latest()->get();
            $data2 = DB::select('SELECT p.productID,p.name,GROUP_CONCAT(c.name) as category,p.quantity,p.quantitySold ,p.price,p.description,p.img,p.onMarket FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID');
            return Datatables::of($data2)
                //->addIndexColumn()
                
                ->addColumn('check', function ($row) {
                    $check = '<input type="checkbox" data-id="' . $row->productID . '">';
                    return $check;
                })
                ->addIndexColumn()

                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="products" data-id="' . $row->productID . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="products" data-id="' . $row->productID . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';

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

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {

        Product::updateOrCreate(['productID' => $request->product_id],
            ['id' => 1,
                'name' => $request->name,
                'img' => "123",
                'category' => $request->category,
                'price' => $request->price,
                'quantity' => $request->quantity,
                'quantitySold' => $request->quantitySold,
                'description' => $request->description]);
        // $product = Product::where('productID',3)->first();
        // $product->name = 'p33';
        // $product->save();
        return response()->json(['success' => 'Product saved successfully.']);
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
        $product = Product::where('productID', $id)->first();
        $product->img = 'tt';
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

        return response()->json(['success' => 'Product  successfully.']);
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

        return response()->json(['success' => 'Product deleted successfully.']);

    }

}
