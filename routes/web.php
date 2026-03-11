<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

use function Symfony\Component\String\s;

// Default landing page (redirects to login or dashboard)
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Keep old URL working (redirect to new dashboard route)
    Route::get('/admin/dashboard', function () {
        return redirect()->route('dashboard');
    });



    Route::get('sales',[SaleController::class,'index'])->name('sale.index');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

    // Items
    Route::get('/items', [ItemController::class, 'index'])->name('items');

    // Parties
    Route::get('/parties', [PartyController::class, 'index'])->name('parties');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
