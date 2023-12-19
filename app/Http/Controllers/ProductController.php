<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    function dashboard()
    {
        // Get total sales for today
        $todaysSales = DB::table('transactions')
            ->whereDate('created_at', now()->toDateString())
            ->sum('total_price');

        // Get total sales for yesterday
        $yesterdaysSales = DB::table('transactions')
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->sum('total_price');

        // Get total sales for this month
        $thisMonthsSales = DB::table('transactions')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_price');

        // Get total sales for last month
        $lastMonthsSales = DB::table('transactions')
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('total_price');

        return view('dashboard', compact('todaysSales',
                                        'yesterdaysSales', 
                                        'thisMonthsSales', 
                                        'lastMonthsSales'));
    }



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


    function sale()
    {
        $products = DB::table("products")->get();

        return view('pages.productSale', [
            'products' => $products
        ]);
    }
    function saleStore(Request $request)
    {

        // Get product information from the products table
        $product = DB::table('products')->where('id', $request->input('name'))->first();
        $p_quantity = $product->quantity;
        $t_quantity = $request->input('quantity');

        if ($p_quantity < 1) {
            // Quantity is less than 1, prevent sale
            return redirect()->back()->with('error', 'Stock Out');
        } elseif ($p_quantity < $t_quantity) {
            return redirect()->back()->with('error', 'Low Stock');
        }

        // Calculate sale quantity
        $update_quantity = $p_quantity - $t_quantity;

        // Calculate total price
        $totalPrice = $product->price * $request->input('quantity');

        // Insert data into the transactions table
        DB::table('transactions')->insert([
            'customer_name' => $request->input('customer_name'),
            'product_id' => $product->id,
            'product_name' => $product->product_name,
            'quantity' => $request->input('quantity'),
            'unit_price' => $product->price,
            'total_price' => $totalPrice
        ]);

        DB::table('products')->where('id', $product->id)->update([
            'quantity' => $update_quantity
        ]);

        return redirect()->route('pages.transactions')->with('success','Successfully sold ');
    }


    function transactions()
    {

        //return view('pages.transactions', compact('transactions'));
        $transactions = DB::table("transactions")->paginate(4);

        return view('pages.transactions', [
            'transactions' => $transactions
        ]);
    }
}
