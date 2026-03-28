<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', fn() => redirect()->route('dashboard'));

    Route::get('sales', [SaleController::class, 'index'])->name('sale.index');

    Route::get('/invoice',       [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

    // ── Items ──────────────────────────────────────────────────────────
    // RULE: every static segment BEFORE any {wildcard} route

    Route::get( '/items',         [ItemController::class, 'index'])->name('items');
    Route::get( '/items/services',[ItemController::class, 'services'])->name('items.services');
    Route::get( '/items/create',  [ItemController::class, 'create'])->name('items.create');
    Route::post('/items',         [ItemController::class, 'store'])->name('items.store');

    // Category — GET /list BEFORE PUT/DELETE /{id}
    Route::get(   '/items/category',      [ItemController::class, 'category'])->name('items.category');
    Route::get(   '/items/category/list', [ItemController::class, 'categoryList'])->name('items.category.list');
    Route::post(  '/items/category',      [ItemController::class, 'storeCategory'])->name('items.category.store');
    Route::put(   '/items/category/{id}', [ItemController::class, 'updateCategory'])->name('items.category.update');
    Route::delete('/items/category/{id}', [ItemController::class, 'destroyCategory'])->name('items.category.destroy');

    // Units — same pattern
    Route::get(   '/items/units',      [ItemController::class, 'units'])->name('items.units');
    Route::post(  '/items/units',      [ItemController::class, 'storeUnit'])->name('items.units.store');
    Route::put(   '/items/units/{id}', [ItemController::class, 'updateUnit'])->name('items.units.update');
    Route::delete('/items/units/{id}', [ItemController::class, 'destroyUnit'])->name('items.units.destroy');

    // Item {id} wildcard — LAST
    Route::get(   '/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put(   '/items/{id}',      [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}',      [ItemController::class, 'destroy'])->name('items.destroy');

    // Parties
    Route::get('/parties', [PartyController::class, 'index'])->name('parties');

    // Profile
    Route::get(   '/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch( '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';