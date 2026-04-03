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
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SaleOrderController;
use App\Http\Controllers\PurchaseExpenseController;
use App\Http\Controllers\PurchaseBillController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentInController;
use App\Http\Controllers\PerfomaController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ExpenseCreateController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;

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
    Route::get('/sales/{sale}/invoice-preview', [SaleController::class, 'invoicePreview'])->name('sale.invoice-preview');
    Route::get('/sales/{sale}/invoice-pdf', [SaleController::class, 'invoicePdf'])->name('sale.invoice-pdf');
    Route::get('/sales/{sale}/delivery-preview', [SaleController::class, 'deliveryPreview'])->name('sale.delivery-preview');
    Route::get('/sales/{sale}/payment-history', [SaleController::class, 'paymentHistory'])->name('sale.payment-history');
    Route::get('/sales/{sale}/bank-history', [SaleController::class, 'bankHistory'])->name('sale.bank-history');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sale.cancel');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sale.destroy');
    Route::get('sales/pos', [SaleController::class, 'pos1'])->name('sale.pos');



    Route::get('/estimates/{sale}/convert-to-sale', [SaleController::class, 'createFromEstimate'])->name('estimates.convert-to-sale');
    Route::get('/estimates/{sale}/edit', [SaleController::class, 'edit'])->name('estimates.edit');
    Route::delete('/estimates/{sale}', [SaleController::class, 'destroy'])->name('estimates.destroy');
    Route::get('/estimates/{sale}/preview', [SaleController::class, 'previewEstimate'])->name('estimates.preview');
    Route::get('/estimates/{sale}/print', [SaleController::class, 'printEstimate'])->name('estimates.print');
    Route::get('/estimates/{sale}/pdf', [SaleController::class, 'pdfEstimate'])->name('estimates.pdf');
    Route::get('/sale-orders/{sale}/convert-to-sale', [SaleController::class, 'createFromSaleOrder'])->name('sale-orders.convert-to-sale');
    Route::get('/delivery-challans/{sale}/convert-to-sale', [SaleController::class, 'createFromDeliveryChallan'])->name('delivery-challans.convert-to-sale');
    Route::get('/sale-orders/{sale}/preview', [SaleController::class, 'previewSaleOrder'])->name('sale-orders.preview');
    Route::get('/sale-orders/{sale}/print', [SaleController::class, 'printSaleOrder'])->name('sale-orders.print');
    Route::get('/sale-orders/{sale}/pdf', [SaleController::class, 'pdfSaleOrder'])->name('sale-orders.pdf');

    // Estimates
    Route::get('sales/estimate', [EstimateController::class, 'index'])->name('sale.estimate');
    Route::get('estimate/create', [EstimateController::class, 'create'])->name('sale.estimate.create');
    Route::get('estimates/create', [EstimateController::class, 'create'])->name('estimates.create');
    Route::post('/estimates', [EstimateController::class, 'store'])->name('estimate.store');

    // Sale Sections
    Route::get('/payment-in', [SaleSectionController::class, 'paymentIn'])->name('payment-in');



    Route::get('/proforma-invoice', [PerfomaController::class, 'proformaInvoice'])->name('proforma-invoice');
    Route::get('/proforma-invoice/create', [PerfomaController::class, 'createProformaInvoice'])->name('proforma-invoice.create');
    Route::post('/proforma-invoice', [PerfomaController::class, 'store'])->name('proforma-invoice.store');
    Route::get('/proforma-invoice/{sale}/edit', [PerfomaController::class, 'edit'])->name('proforma-invoice.edit');
    Route::put('/proforma-invoice/{sale}', [PerfomaController::class, 'update'])->name('proforma-invoice.update');
    Route::delete('/proforma-invoice/{sale}', [PerfomaController::class, 'destroy'])->name('proforma-invoice.destroy');
    Route::get('/proforma-invoice/{sale}/preview', [PerfomaController::class, 'preview'])->name('proforma-invoice.preview');
    Route::get('/proforma-invoice/{sale}/print', [PerfomaController::class, 'print'])->name('proforma-invoice.print');
    Route::get('/proforma-invoice/{sale}/pdf', [PerfomaController::class, 'pdf'])->name('proforma-invoice.pdf');
    Route::get('/proforma-invoice/{sale}/duplicate', [PerfomaController::class, 'duplicate'])->name('proforma-invoice.duplicate');
    Route::get('/proforma-invoice/{sale}/convert-to-sale', [SaleController::class, 'createFromProforma'])->name('proforma-invoice.convert-to-sale');
    Route::get('/proforma-invoice/{sale}/convert-to-sale-order', [SaleOrderController::class, 'createFromProforma'])->name('proforma-invoice.convert-to-sale-order');
    // Sale Return
    Route::get('/sale-return', [SaleReturnController::class, 'saleReturn'])->name('sale-return');
    Route::get('/sale-return/create', [SaleReturnController::class, 'salereturncreate'])->name('sale-return.create');
    Route::post('/sale-return', [SaleReturnController::class, 'store'])->name('sale-return.store');
    Route::get('/sale-return/{sale}/edit', [SaleReturnController::class, 'edit'])->name('sale-return.edit');
    Route::put('/sale-return/{sale}', [SaleReturnController::class, 'update'])->name('sale-return.update');
    Route::delete('/sale-return/{sale}', [SaleReturnController::class, 'destroy'])->name('sale-return.destroy');
    Route::get('/sale-return/{sale}/preview', [SaleReturnController::class, 'preview'])->name('sale-return.preview');
    Route::get('/sale-return/{sale}/print', [SaleReturnController::class, 'print'])->name('sale-return.print');
    Route::get('/sale-return/{sale}/pdf', [SaleReturnController::class, 'pdf'])->name('sale-return.pdf');
    Route::get('/sale-return/{sale}/duplicate', [SaleReturnController::class, 'duplicate'])->name('sale-return.duplicate');

    // Delivery Challan
    Route::get('delivery-challan', [DeliveryController::class, 'deliveryChallan'])->name('delivery-challan');
    Route::get('create-challan', [DeliveryController::class, 'createChallan'])->name('create-challan');
    Route::post('delivery-challan', [DeliveryController::class, 'store'])->name('delivery-challan.store');
    Route::get('delivery-challan/{sale}/edit', [DeliveryController::class, 'edit'])->name('delivery-challan.edit');
    Route::put('delivery-challan/{sale}', [DeliveryController::class, 'update'])->name('delivery-challan.update');
    Route::delete('delivery-challan/{sale}', [DeliveryController::class, 'destroy'])->name('delivery-challan.destroy');
    Route::get('delivery-challan/{sale}/preview', [DeliveryController::class, 'preview'])->name('delivery-challan.preview');
    Route::get('delivery-challan/{sale}/print', [DeliveryController::class, 'print'])->name('delivery-challan.print');
    Route::get('delivery-challan/{sale}/pdf', [DeliveryController::class, 'pdf'])->name('delivery-challan.pdf');
    Route::get('delivery-challan/{sale}/duplicate', [DeliveryController::class, 'duplicate'])->name('delivery-challan.duplicate');

    // Sale Orders
    Route::get('sale-order', [SaleOrderController::class, 'saleOrder'])->name('sale-order');
    Route::get('sale-order/create', [SaleOrderController::class, 'create'])->name('sale-order.create');
    Route::get('estimates/{sale}/convert-to-sale-order', [SaleOrderController::class, 'createFromEstimate'])->name('estimates.convert-to-sale-order');

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
    Route::post('/bank-accounts/transfer', [BankAccountController::class, 'transfer'])->name('bank-accounts.transfer');
    Route::get('cash-in-hand', [BankAccountController::class, 'cashInHand'])->name('cash-in-hand');
    Route::get('/bank-accounts/{bankAccount}', [BankAccountController::class, 'show'])->name('bank-accounts.show');
    Route::get('/bank-accounts/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('bank-accounts.edit');
    Route::put('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])->name('bank-accounts.update');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');

    // Purchase & Expenses



    Route::get('/purchase-bill', [PurchaseBillController::class, 'purchaseExpenses'])->name('purchase-expenses');
    Route::get('/purchase-bill/create', [PurchaseBillController::class, 'create'])->name('purchase-bill.create');
    Route::post('/purchase-bills', [PurchaseBillController::class, 'store'])->name('purchase-bills.store');
    Route::get('/purchase-bills/{purchase}/edit', [PurchaseBillController::class, 'edit'])->name('purchase-bills.edit');
    Route::put('/purchase-bills/{purchase}', [PurchaseBillController::class, 'update'])->name('purchase-bills.update');
    Route::delete('/purchase-bills/{purchase}', [PurchaseBillController::class, 'destroy'])->name('purchase-bills.destroy');
    Route::get('/purchase-bills/{purchase}/preview', [PurchaseBillController::class, 'preview'])->name('purchase-bills.preview');
    Route::get('/purchase-bills/{purchase}/print', [PurchaseBillController::class, 'print'])->name('purchase-bills.print');
    Route::get('/purchase-bills/{purchase}/pdf', [PurchaseBillController::class, 'pdf'])->name('purchase-bills.pdf');



    Route::get('purchase-order', [PurchaseOrderController::class, 'purchaseOrder'])->name('purchase-order');
    Route::get('purchase-order/create', [PurchaseOrderController::class, 'create'])->name('purchase-order.create');
    Route::get('purchase-orders/{purchase}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('purchase-orders/{purchase}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
    Route::get('purchase-orders/{purchase}/preview', [PurchaseOrderController::class, 'preview'])->name('purchase-orders.preview');
    Route::get('purchase-orders/{purchase}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::get('purchase-orders/{purchase}/pdf', [PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
    Route::get('purchase-orders/{purchase}/history', [PurchaseOrderController::class, 'history'])->name('purchase-orders.history');
    Route::put('purchase-orders/{purchase}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
    Route::delete('purchase-orders/{purchase}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');


    Route::get('settings/general', [SettingController::class, 'general'])->name('settings.general');
    Route::get('settings/transactions', [SettingController::class, 'transactions'])->name('settings.transactions');
    Route::get('settings/taxes', [SettingController::class, 'taxes'])->name('settings.taxes');
    Route::get('settings/items', [SettingController::class, 'items'])->name('settings.items');
    Route::get('settings/parties', [SettingController::class, 'parties'])->name('settings.parties');
    Route::get('settings/transaction-messages', [SettingController::class, 'transactionMessages'])->name('settings.transaction-messages');
    Route::get('settings/print-layout', [SettingController::class, 'printLayout'])->name('settings.print-layout');




    Route::get('/payment-out', [PurchaseExpenseController::class, 'paymentOut'])->name('payment-out');
    Route::get('expense', [PurchaseExpenseController::class, 'expense'])->name('expense');
    Route::get('purchase-return', [PurchaseReturnController::class, 'index'])->name('purchase-return');
    Route::get('purchase-return/create', [PurchaseReturnController::class, 'create'])->name('purchase-return.create');
    Route::post('/purchase-return', [PurchaseReturnController::class, 'store'])->name('purchase-return.store');
    Route::get('/purchase-return/{purchase}/edit', [PurchaseReturnController::class, 'edit'])->name('purchase-return.edit');
    Route::put('/purchase-return/{purchase}', [PurchaseReturnController::class, 'update'])->name('purchase-return.update');
    Route::delete('/purchase-return/{purchase}', [PurchaseReturnController::class, 'destroy'])->name('purchase-return.destroy');
    Route::get('/purchase-return/{purchase}/preview', [PurchaseReturnController::class, 'preview'])->name('purchase-return.preview');
    Route::get('/purchase-return/{purchase}/print', [PurchaseReturnController::class, 'print'])->name('purchase-return.print');
    Route::get('/purchase-return/{purchase}/pdf', [PurchaseReturnController::class, 'pdf'])->name('purchase-return.pdf');
    Route::get('/purchase-return/{purchase}/duplicate', [PurchaseReturnController::class, 'duplicate'])->name('purchase-return.duplicate');




Route::get('reports', [ReportController::class, 'index'])->name('reports');



// ═══════════════════════════════════════
// ADD THESE ROUTES inside the auth middleware group in web.php
// Replace the two existing expense routes:
//   Route::get('expense', [ExpenseCreateController::class, 'expense'])->name('expense');
//   Route::get('expense/create', [ExpenseCreateController::class, 'createExpense'])->name('expense.create');
// WITH THESE:
// ═══════════════════════════════════════

Route::get('expense', [ExpenseCreateController::class, 'expense'])->name('expense');

// Expense Categories
Route::post('expense/categories', [ExpenseCreateController::class, 'storeCategory'])->name('expense.categories.store');
Route::delete('expense/categories/{id}', [ExpenseCreateController::class, 'destroyCategory'])->name('expense.categories.destroy');
Route::put('expense/categories/{id}', [ExpenseCreateController::class, 'updateCategory'])->name('expense.categories.update');

// Expense Items
Route::post('expense/items', [ExpenseCreateController::class, 'storeItem'])->name('expense.items.store');
Route::put('expense/items/{id}', [ExpenseCreateController::class, 'updateItem'])->name('expense.items.update');
Route::delete('expense/items/{id}', [ExpenseCreateController::class, 'destroyItem'])->name('expense.items.destroy');

// Expenses
Route::post('expense/save', [ExpenseCreateController::class, 'storeExpense'])->name('expense.save');
Route::delete('expense/{id}', [ExpenseCreateController::class, 'destroyExpense'])->name('expense.destroy');






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

 Route::post('/items/{id}/adjust', [ItemController::class, 'adjust'])->name('items.adjust');
Route::get('/items/{id}/transactions', [ItemController::class, 'transactions'])->name('items.transactions');
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');
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
    // payment-in
    Route::get('/payment-in', [SaleSectionController::class, 'paymentIn'])->name('payment-in');
Route::post('/payments-in', [BankAccountController::class, 'paymentIn'])->name('payments-in.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Debug pages
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

      Route::get('/theme', function () {
        return view('themes.index');
    })->name('theme');

});


require __DIR__.'/auth.php';
