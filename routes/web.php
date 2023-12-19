<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Login & Signup Page

Route::get('/', function () {
    return view('welcome');
});


// Routes for Profile Page

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Routes for Dashboard Page

Route::get('/dashboard',[DashboardController::class, 'dashboard' ])->middleware(['auth', 'verified'])->name('dashboard')->prefix('product');


// Routes for Products Page 

Route::prefix('product')->middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'products'])->name('pages.products');
    Route::get('/create', [ProductController::class, 'create'])->name('pages.create');
    Route::post('/create', [ProductController::class, 'store'])->name('pages.create');
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('pages.edit');
    Route::put('/edit/{id}', [ProductController::class, 'update'])->name('pages.edit');
    Route::get('/delete/{id}', [ProductController::class, 'delete'])->name('pages.delete');
    
});


// Routes for Sale Page

Route::prefix('product')->middleware('auth')->group(function () {
    Route::get('/sale', [SaleController::class, 'sale'])->name('pages.sale');
    Route::post('/sale', [SaleController::class, 'saleStore'])->name('pages.sale');
});


// Routes for Transaction Page

Route::prefix('product')->middleware('auth')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'transactions'])->name('pages.transactions');
});


require __DIR__.'/auth.php';
