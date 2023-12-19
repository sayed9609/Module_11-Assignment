<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    function transactions()
    {

        $transactions = DB::table("transactions")->paginate(4);

        return view('pages.transactions', [
            'transactions' => $transactions
        ]);
    }
}
