<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleSectionController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\PurchaseExpenseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
 Route::get('parties/{party}/transactions', [PartyController::class, 'transactions'])
        ->name('parties.transactions');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Sales
    Route::get('/sale/create', [SaleController::class,'create'])->name('sale.create');
    Route::get('/sales/{sale}/edit', [SaleController::class,'edit'])->name('sale.edit');
    Route::put('/sales/{sale}', [SaleController::class,'update'])->name('sale.update');
    Route::delete('/sales/{sale}', [SaleController::class,'destroy'])->name('sale.destroy');
    Route::post('/sales', [SaleController::class,'store'])->name('sale.store');
    Route::get('/sales', [SaleController::class,'index'])->name('sale.index');
    Route::get('sales/estimate', [EstimateController::class,'index'])->name('sale.estimate');
    Route::get('estimate/create', [SaleController::class,'estimatcreate'])->name('sale.estimate.create');
    Route::get('sales/pos', [SaleController::class,'pos1'])->name('sale.pos');

    // Estimates
    Route::get('/estimates', [EstimateController::class,'index'])->name('estimates.index');
    Route::get('/estimates/create', [EstimateController::class,'create'])->name('estimates.create');
    Route::get('/estimates/{estimate}/convert-to-sale', [SaleController::class,'createFromEstimate'])->name('estimates.convert-to-sale');
    Route::get('/estimates/{estimate}/print', [EstimateController::class,'print'])->name('estimates.print');
    Route::get('/estimates/{estimate}/preview', [EstimateController::class,'preview'])->name('estimates.preview');
    Route::get('/estimates/{estimate}/pdf', [EstimateController::class,'pdf'])->name('estimates.pdf');
    Route::post('/estimates/{estimate}/convert', [EstimateController::class,'convert'])->name('estimates.convert');
    Route::get('/estimates/{estimate}', [EstimateController::class,'show'])->name('estimates.show');
    Route::get('/estimates/{estimate}/edit', [EstimateController::class,'edit'])->name('estimates.edit');
    Route::put('/estimates/{estimate}', [EstimateController::class,'update'])->name('estimates.update');
    Route::delete('/estimates/{estimate}', [EstimateController::class,'destroy'])->name('estimates.destroy');
    Route::post('/estimates', [EstimateController::class,'store'])->name('estimate.store');


    // Sale Sections
    Route::get('/payment-in', [SaleSectionController::class, 'paymentIn'])->name('payment-in');
    Route::get('/proforma-invoice', [SaleSectionController::class, 'proformaInvoice'])->name('proforma-invoice');
    Route::get('/sale-return', [SaleSectionController::class, 'saleReturn'])->name('sale-return');
 Route::get('delivery-challan', [SaleSectionController::class, 'deliveryChallan'])->name('delivery-challan');
 Route::get('sale-order' ,[SaleSectionController::class, 'saleOrder'])->name('sale-order');

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

    Route::get('cash-in-hand', [BankAccountController::class, 'cashInHand'])->name('cash-in-hand');
    // Support fetching a single bank account for view/edit via AJAX
    Route::get('/bank-accounts/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('/bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
    Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');

Route::get('/purchase-bill', [PurchaseExpenseController::class, 'purchaseExpenses'])->name('purchase-expenses');
Route::get('/payment-out', [PurchaseExpenseController::class, 'paymentOut'])->name('payment-out');
Route::get('purchase-order', [PurchaseExpenseController::class, 'purchaseOrder'])->name('purchase-order');
Route::get('expense', [PurchaseExpenseController::class, 'expense'])->name('expense');
Route::get('purchase-return', [PurchaseExpenseController::class, 'purchaseReturn'])->name('purchase-return');

    // Items
    Route::get('/items', [ItemController::class, 'index'])->name('items');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    // Parties
Route::get('/parties', [PartyController::class, 'index'])->name('parties');


    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Debug pages - admin/user verification
    Route::get('/debug/admin', function () {
        return view('debug.admin_test');
    })->name('debug.admin');

    Route::get('/debug/user', function () {
        return view('debug.user_test');
    })->name('debug.user');

    // Sidebar test pages
    Route::get('/test/admin-sidebar', function () {
        return view('dashboard.test_admin_sidebar');
    })->name('test.admin_sidebar');

    Route::get('/test/user-sidebar', function () {
        return view('dashboard.test_user_sidebar');
    })->name('test.user_sidebar');

});

require __DIR__.'/auth.php';


// GET route to show parties page
