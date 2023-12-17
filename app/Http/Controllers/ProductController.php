<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    function dashboard()
    {
        $currentDate = Carbon::now()->toDateString();
        $yesterdayDate = Carbon::yesterday()->toDateString();
        $currentMonth = Carbon::now()->startOfMonth()->toDateString();
        $currentMonthEnd = Carbon::now()->endOfMonth()->toDateString();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $todaysSales = DB::table('transactions')
            ->selectRaw('SUM(total_price) as total_sales')
            ->whereDate('created_at', $currentDate)
            ->first();

        $yesterdaysSales = DB::table('transactions')
            ->selectRaw('SUM(total_price) as total_sales')
            ->whereDate('created_at', $yesterdayDate)
            ->first();

        $thisMonthsSales = DB::table('transactions')
            ->selectRaw('SUM(total_price) as total_sales')
            ->whereBetween('created_at', [$currentMonth, $currentMonthEnd])
            ->first();

        $lastMonthsSales = DB::table('transactions')
            ->selectRaw('SUM(total_price) as total_sales')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->first();

        return view('dashboard')->with([
            'todaysSales' => $todaysSales,
            'yesterdaysSales' => $yesterdaysSales,
            'thisMonthsSales' => $thisMonthsSales,
            'lastMonthsSales' => $lastMonthsSales,
        ]);
    }



    function products()
    {
        // $products = Product::paginate(5);
        $products = DB::table("products")->get();

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


        return redirect()->route('pages.products');
    }

    function edit($id)
    {
        $products = DB::table('products')->find($id);
        return view('pages.productEdit', ['products' => $products]);
    }

    function update(Request $request, $id)
    {
        DB::table('products')->where('id', $id)->update([
            'product_name' => $request->input('name'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price')
        ]);
        return redirect()->route('pages.products');
    }

    function delete($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return redirect()->route('pages.products');
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

        // Calculate 
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

        return redirect()->route('pages.transactions');
    }


    function transactions()
    {

        //return view('pages.transactions', compact('transactions'));
        $transactions = DB::table("transactions")->get();

        return view('pages.transactions', [
            'transactions' => $transactions
        ]);
    }
}
