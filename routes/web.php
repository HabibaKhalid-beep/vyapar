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
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\PurchaseExpenseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) return redirect()->route('dashboard');
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', fn() => redirect()->route('dashboard'));

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('sale.index');
    Route::get('/sale/create/{type?}', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sale.store');
    Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sale.edit');
    Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sale.update');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::get('/sales/pos', [SaleController::class, 'pos1'])->name('sale.pos');

    // Estimates
    Route::get('sales/estimate', [EstimateController::class, 'index'])->name('sale.estimate');
    Route::get('estimates/create', [EstimateController::class, 'create'])->name('estimates.create');
    Route::post('/estimates', [EstimateController::class, 'store'])->name('estimate.store');
    Route::get('/estimates/{sale}/edit', [SaleController::class, 'edit'])->name('estimates.edit');
    Route::delete('/estimates/{sale}', [SaleController::class, 'destroy'])->name('estimates.destroy');
    Route::get('/estimates/{sale}/preview', [SaleController::class, 'previewEstimate'])->name('estimates.preview');
    Route::get('/estimates/{sale}/print', [SaleController::class, 'printEstimate'])->name('estimates.print');
    Route::get('/estimates/{sale}/pdf', [SaleController::class, 'pdfEstimate'])->name('estimates.pdf');
    Route::get('/estimates/{sale}/convert-to-sale', [SaleController::class, 'createFromEstimate'])->name('estimates.convert-to-sale');
    Route::get('estimates/{sale}/convert-to-sale-order', [SaleOrderController::class, 'createFromEstimate'])->name('estimates.convert-to-sale-order');

    // Sale Orders
    Route::get('sale-order', [SaleOrderController::class, 'saleOrder'])->name('sale-order');
    Route::get('sale-order/create', [SaleOrderController::class, 'create'])->name('sale-order.create');
    Route::get('/sale-orders/{sale}/convert-to-sale', [SaleController::class, 'createFromSaleOrder'])->name('sale-orders.convert-to-sale');
    Route::get('/sale-orders/{sale}/preview', [SaleController::class, 'previewSaleOrder'])->name('sale-orders.preview');
    Route::get('/sale-orders/{sale}/print', [SaleController::class, 'printSaleOrder'])->name('sale-orders.print');
    Route::get('/sale-orders/{sale}/pdf', [SaleController::class, 'pdfSaleOrder'])->name('sale-orders.pdf');

    // Sale Sections
    Route::get('/payment-in', [SaleSectionController::class, 'paymentIn'])->name('payment-in');
    Route::get('/proforma-invoice', [SaleSectionController::class, 'proformaInvoice'])->name('proforma-invoice');
    Route::get('/sale-return', [SaleSectionController::class, 'saleReturn'])->name('sale-return');
    Route::get('delivery-challan', [SaleSectionController::class, 'deliveryChallan'])->name('delivery-challan');

    // Invoice
    Route::get('/invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('/invoice/print', [InvoiceController::class, 'print'])->name('invoice.print');

    // Purchase & Expenses
    Route::get('/purchase-bill', [PurchaseExpenseController::class, 'purchaseExpenses'])->name('purchase-expenses');
    Route::get('/payment-out', [PurchaseExpenseController::class, 'paymentOut'])->name('payment-out');
    Route::get('purchase-order', [PurchaseExpenseController::class, 'purchaseOrder'])->name('purchase-order');
    Route::get('expense', [PurchaseExpenseController::class, 'expense'])->name('expense');
    Route::get('purchase-return', [PurchaseExpenseController::class, 'purchaseReturn'])->name('purchase-return');

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
    Route::get('/bank-accounts/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('/bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
    Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');

    // Items — static routes BEFORE wildcard {id} routes
    Route::get('/items', [ItemController::class, 'index'])->name('items');
    Route::get('/items/services', [ItemController::class, 'services'])->name('items.services');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    Route::get('/items/category', [ItemController::class, 'category'])->name('items.category');
    Route::get('/items/category/list', [ItemController::class, 'categoryList'])->name('items.category.list');
    Route::post('/items/category', [ItemController::class, 'storeCategory'])->name('items.category.store');
    Route::put('/items/category/{id}', [ItemController::class, 'updateCategory'])->name('items.category.update');
    Route::delete('/items/category/{id}', [ItemController::class, 'destroyCategory'])->name('items.category.destroy');

    Route::get('/items/units', [ItemController::class, 'units'])->name('items.units');
    Route::post('/items/units', [ItemController::class, 'storeUnit'])->name('items.units.store');
    Route::put('/items/units/{id}', [ItemController::class, 'updateUnit'])->name('items.units.update');
    Route::delete('/items/units/{id}', [ItemController::class, 'destroyUnit'])->name('items.units.destroy');

    Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Parties
    Route::get('/parties', [PartyController::class, 'index'])->name('parties');
    Route::post('/parties', [PartyController::class, 'store'])->name('parties.store');
    Route::get('/parties/{party}', [PartyController::class, 'show'])->name('parties.show');
    Route::put('/parties/{party}', [PartyController::class, 'update'])->name('parties.update');
    Route::delete('/parties/{id}', [PartyController::class, 'destroy'])->name('parties.destroy');
    Route::get('parties/{party}/transactions', [PartyController::class, 'transactions'])->name('parties.transactions');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Debug pages
    Route::get('/debug/admin', fn() => view('debug.admin_test'))->name('debug.admin');
    Route::get('/debug/user', fn() => view('debug.user_test'))->name('debug.user');

    // Sidebar test pages
    Route::get('/test/admin-sidebar', fn() => view('dashboard.test_admin_sidebar'))->name('test.admin_sidebar');
    Route::get('/test/user-sidebar', fn() => view('dashboard.test_user_sidebar'))->name('test.user_sidebar');

});

require __DIR__.'/auth.php';