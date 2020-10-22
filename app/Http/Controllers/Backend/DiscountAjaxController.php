<?php

namespace App\Http\Controllers\Backend;

use App\Discount;
use App\Products_Discounts;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountAjaxController extends Controller
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
            $data = Discount::get();
            foreach ($data as $key => $row) {
                if($row->method==2){
                    $row->discount = $row->discount.'%';
                }else{
                    $row->discount = '$'.$row->discount;
                }
            }
             return Datatables::of($data)
                ->addIndexColumn()

               
                ->addColumn('action', function ($row) {
                   
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm edit">編輯</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete">刪除</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Set" class="btn btn-success btn-sm setProduct">加入商品</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="discount" data-id="' . $row->id . '" data-original-title="Set" class="btn btn-success btn-sm removeProduct">移除商品</a>';

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
        if($request->method ==2){
            if($request->discount>=100){
                return response()->json(['error' => '確認優惠內容,請小於100%']);
            }
        }
        if (!is_numeric($request->total) 
            || !is_numeric($request->discount)
            || !is_numeric($request->user_lv)
            || !is_numeric($request->method)) {
            return response()->json(['error' => '僅能輸入數字']);
        }

        if ($request->total <=0 || $request->discount <=0) {
            return response()->json(['error' => '請輸入大於0的數字']);
        }
        if ($request->user_lv > 10 || $request->user_lv < 0) {
            return response()->json(['error' => '單筆訂單升級限制不得大於10等或小於0等']);
        }
        if($request->method<0 || $request->method>2){
            return response()->json(['error' => '輸入錯誤']);
        }
        
        Discount::updateOrCreate(['id' => $request->id],
            [
                'name' => $request->name,
                'method' => $request->method,
                'total' => $request->total,
                'discount' => $request->discount,
                'user_lv' => $request->user_lv,
            ]);

        return response()->json(['success' => '成功儲存優惠.']);
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

        return response()->json(['success' => '成功刪除優惠.']);

    }

}
