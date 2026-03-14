<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

// Default landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sales
    Route::get('/sale/create', [SaleController::class,'create'])->name('sale.create');
    Route::get('/sales', [SaleController::class,'index'])->name('sale.index');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

    // Items
    Route::get('/items', [ItemController::class, 'index'])->name('items');

    // Parties
    Route::get('/parties', [PartyController::class, 'index'])->name('parties');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
