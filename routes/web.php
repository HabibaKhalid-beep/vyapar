<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseExpenseController;

use Illuminate\Support\Facades\Route;

// Default landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {


// POST route to save new party
Route::post('/parties', [PartyController::class, 'store'])->name('parties.store');

Route::get('/parties/{party}', [PartyController::class, 'show'])->name('parties.show');
Route::put('/parties/{party}', [PartyController::class, 'update'])->name('parties.update');

Route::get('/parties/{id}', [PartyController::class,'show']);
Route::put('/parties/{id}', [PartyController::class,'update']);
Route::delete('/parties/{id}', [PartyController::class,'destroy']);

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sales
    Route::get('/sale/create', [SaleController::class,'create'])->name('sale.create');
    Route::get('/sales/{sale}/edit', [SaleController::class,'edit'])->name('sale.edit');
    Route::put('/sales/{sale}', [SaleController::class,'update'])->name('sale.update');
    Route::delete('/sales/{sale}', [SaleController::class,'destroy'])->name('sale.destroy');
    Route::post('/sales', [SaleController::class,'store'])->name('sale.store');
    Route::get('/sales', [SaleController::class,'index'])->name('sale.index');
    Route::get('sales/estimate', [SaleController::class,'estimate'])->name('sale.estimate');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

    // Loan Accounts
    Route::get('/loan-accounts', [LoanAccountController::class, 'index'])->name('loan-accounts');
    Route::post('/loan-accounts', [LoanAccountController::class, 'store'])->name('loan-accounts.store');
    Route::get('/loan-accounts/{loanAccount}', [LoanAccountController::class, 'show'])->name('loan-accounts.show');
    Route::get('/loan-accounts/{loanAccount}/edit', [LoanAccountController::class, 'edit'])->name('loan-accounts.edit');
    Route::put('/loan-accounts/{loanAccount}', [LoanAccountController::class, 'update'])->name('loan-accounts.update');
    Route::delete('/loan-accounts/{loanAccount}', [LoanAccountController::class, 'destroy'])->name('loan-accounts.destroy');

    // Bank Accounts
    Route::get('/bank-accounts', [BankAccountController::class, 'index'])->name('bank-accounts');
    Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
    // Support fetching a single bank account for view/edit via AJAX
    Route::get('/bank-accounts/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('/bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
    Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');

Route::get('/purchase-bill', [PurchaseExpenseController::class, 'purchaseExpenses'])->name('purchase-expenses');
Route::get('/payment-out', [PurchaseExpenseController::class, 'paymentOut'])->name('payment-out');

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


// GET route to show parties page
