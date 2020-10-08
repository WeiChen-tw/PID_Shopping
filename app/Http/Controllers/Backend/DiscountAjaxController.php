<?php

namespace App\Http\Controllers\Backend;

use App\Discount;
use App\Products_Discounts;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Discount::get();

             return Datatables::of($data)
                ->addIndexColumn()

               
                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete">Delete</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Set" class="btn btn-success btn-sm setProduct">Set Product</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Set" class="btn btn-success btn-sm removeProduct">Remove Product</a>';

                    return $btn;

                })

                ->rawColumns(['action'])
                ->make(true);

        }

        return view('backend.home', compact('discount'));

    }

    public function setProductCategory(Request $request)
    {
        if($request->action=='add'){
            foreach ($request->product_id_arr as $key => $product_id) {
                Products_Categories::updateOrCreate(
                    ['product_id' => $product_id, 'category_id' => $request->id]
                );
            }
            return response()->json(['success' => 'Product Category Add Successfully.']);
        }else if($request->action=='remove'){
            foreach ($request->product_id_arr as $key => $product_id) {
                Products_Categories::where('product_id',$product_id)
                    ->where('category_id',$request->id)
                    ->delete();
            }
            return response()->json(['success' => 'Product Category Remove Successfully.']);
        }
        
        
    }
    public function setProductDiscount(Request $request)
    {
        if($request->action=='add'){
            foreach ($request->product_id_arr as $key => $product_id) {
                Products_Discounts::updateOrCreate(
                    ['product_id' => $product_id, 'discount_id' => $request->id]
                );
            }
            return response()->json(['success' => 'Product Discount Add Successfully.']);
        }else if($request->action=='remove'){
            foreach ($request->product_id_arr as $key => $product_id) {
                Products_Discounts::where('product_id',$product_id)
                    ->where('discount_id',$request->id)
                    ->delete();
            }
            return response()->json(['success' => 'Product Discount Remove Successfully.']);
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

        Discount::updateOrCreate(['id' => $request->id],
            [
                'method' => $request->method,
                'total' => $request->total,
                'discount' => $request->discount,
            ]);

        return response()->json(['success' => 'Discount saved successfully.']);
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Discount  $discount

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        $discount = Discount::find($id);
        //$discount = Discount::where('id', $id)->first();

        //$discount->save();
        return response()->json($discount);
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Discount  $discount

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        Discount::find($id)->delete();

        return response()->json(['success' => 'Discount deleted successfully.']);

    }

}
