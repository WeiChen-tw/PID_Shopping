<?php

namespace App\Http\Controllers\Backend;

use App\Category;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CategoryAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Category::get();
            
            //$data2 = DB::select('SELECT p.productID,c.id,p.name,c.name as category FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID ,c.id,c.name');
            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('check', function ($row) {
                    $check = '<input type="checkbox" data-id="' . $row->productID . '">';
                    return $check;
                })
                ->addIndexColumn()

                ->addColumn('action', function ($row) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="category" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="category" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">Delete</a>';

                    return $btn;

                })
                
                ->rawColumns(['action', 'check'])
                ->make(true);

        }

        return view('backend.home', compact('category'));

    }


    public function getCategoryData(){
        $selectData = DB::select('SELECT * FROM categories');
        return response()->json($selectData);
    }
    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {

        Category::updateOrCreate(['id' => $request->id],
            [
                'name' => $request->name,
            ]);
        
        return response()->json(['success' => 'Category saved successfully.']);
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        $category = Category::find($id);
        //$category = Category::where('id', $id)->first();
        
        //$category->save();
        return response()->json($category);
    }

    
    
    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Category  $category

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        Category::find($id)->delete();

        return response()->json(['success' => 'Category deleted successfully.']);

    }

}
