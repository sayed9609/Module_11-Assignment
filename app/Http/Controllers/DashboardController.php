<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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
}
