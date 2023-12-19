<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    function products()
    {
        // $products = Product::paginate(5);
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

        DB::table('products')->insert([
            'product_name' => $request->input('name'),
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
        DB::table('products')->where('id', $id)->update([
            'product_name' => $request->input('name'),
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
