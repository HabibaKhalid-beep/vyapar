<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;

// Redirect root to dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Invoice
Route::get('/invoice',       [InvoiceController::class, 'index'])->name('invoice');
Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

// Items
Route::get('/items', [ItemController::class, 'index'])->name('items');

// Parties
Route::get('/parties', [PartyController::class, 'index'])->name('parties');
