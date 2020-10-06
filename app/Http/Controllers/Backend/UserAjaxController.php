<?php

namespace App\Http\Controllers\Backend;

use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAjaxController extends Controller
{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = User::get();
            //$data2 = DB::select('SELECT p.productID,p.name,GROUP_CONCAT(c.name) as category,p.quantity,p.quantitySold ,p.price,p.description,p.img,p.onMarket FROM `products` as p INNER JOIN products_categories as pc INNER JOIN categories as c on p.productID = pc.product_id and pc.category_id = c.id GROUP BY p.productID');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('check', function ($row) {
                    $check = '<input type="checkbox" data-id="' . $row->id . '">';
                    return $check;
                })
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-table="user" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm edit">Edit</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="user" data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete">Delete</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="user" data-id="' . $row->id . '" data-original-title="Order" class="btn btn-success btn-sm order">Order</a>';
                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-table="user" data-id="' . $row->id . '" data-original-title="Msg" class="btn btn-link btn-sm order">Msg</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'check'])
                ->make(true);
        }

        return view('backend.home', compact('products'));

    }

    public function getProductData(Request $request)
    {

        if (isset($request->id)) {
            $data = DB::table('products')
                ->join('products_categories', 'products.productID', '=', 'products_categories.product_id')
                ->where('products_categories.category_id', '=', $request->id)
                ->get();
        } else {
            $data = User::all();
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

        User::updateOrCreate(['id' => $request->id],
            [  'name' => $request->name,
                'email' => $request->email,
                'addr' => $request->addr,
                'phone' => $request->phone,
                'coin' => $request->coin,
                'banned' => $request->banned,
                'password' => bcrypt($request->password),
            ]);

        return response()->json(['success' => 'User saved successfully.']);
    }

    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\User  $users

     * @return \Illuminate\Http\Response

     */

    public function edit($id)
    {
        //$users = User::find($id);
        $users = User::where('id', $id)->first();
        
        //$users->save();
        return response()->json($users);
    }

    /**
     * on the market or take off
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function banOrUnban(Request $request)
    {
        foreach ($request->id as $key => $id) {
            $users = User::where('id', $id)->first();
            $users->banned = $request->action;
            $users->save();
        }

        return response()->json(['success' => 'User  successfully.']);
    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\User  $users

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)
    {

        User::find($id)->delete();

        return response()->json(['success' => 'User deleted successfully.']);

    }

}
