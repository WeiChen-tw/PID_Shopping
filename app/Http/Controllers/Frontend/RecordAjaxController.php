<?php

namespace App\Http\Controllers\Frontend;

use App\Record;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class RecordAjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkstatus');
    }
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($request->ajax()) {
            $data = Record::where('user_id',$user->id)->get();

            //$data2 = DB::select('SELECT p.productID,c.id,p.name,c.name as category FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID ,c.id,c.name');
            return Datatables::of($data)
                
                ->make(true);

        }

        return view('frontend.home', compact('record'));

    }

    
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Record  $category

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Record  $category

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        

    }

}
