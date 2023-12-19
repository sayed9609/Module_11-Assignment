<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
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
            // For low Stock
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
}
