<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    function products()
    {
        $products = DB::table("products")->paginate(4);

        return view('pages.products', [
            'products' => $products
        ]);
    }



    function create()
    {
        return view('pages.productCreate');
    }



    function store(Request $request)
    {
        $this->validate($request,[
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|integer'
        ]);

        DB::table('products')->insert([
            'product_name' => $request->input('product_name'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price')
        ]);


        return redirect()->route('pages.products')->with('add', 'Added successfully');
    }



    function edit($id)
    {
        $products = DB::table('products')->find($id);
        return view('pages.productEdit', ['products' => $products])->with('edit', 'Edited successfully');
    }



    function update(Request $request, $id)
    {
        $this->validate($request,[
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|integer'
        ]);

        DB::table('products')->where('id', $id)->update([
            'product_name' => $request->input('product_name'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price')
        ]);
        return redirect()->route('pages.products')->with('update', 'Updated Successfully');
    }



    function delete($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return redirect()->route('pages.products')->with('delete', 'Deleted Successfully');
    }
}
