<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['payments', 'bankAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.sales.sale_index', compact('sales'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        // Pre-generate the next invoice number (based on next sale ID)
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = (string) $nextSaleId;

        return view('dashboard.sales.create', compact('bankAccounts', 'items', 'parties', 'nextInvoiceNumber'));
    }


    public function pos1()
    {

        return view('dashboard.sales.pos');
    }

    public function edit(Sale $sale)
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items', 'payments']);

        // Provide full URLs for existing image/document if stored in the public disk
        if ($sale->image_path) {
            $sale->image_url = Storage::disk('public')->url($sale->image_path);
        }
        if ($sale->document_path) {
            $sale->document_url = Storage::disk('public')->url($sale->document_path);
        }

        return view('dashboard.sales.create', compact('bankAccounts', 'items', 'parties', 'sale'));
    }

    public function update(Request $request, Sale $sale)
    {
        // Validate incoming data (same as store)
        $data = $request->validate([
            'party_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.payment_type' => 'required|string|max:50',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        // When updating, treat payment entries as additional payments (keep previous received amount)
        $existingReceived = floatval($sale->received_amount ?? 0);
        $receivedAmount = $existingReceived;
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                if (!empty($payment['bank_account_id'])) {
                    $receivedAmount += floatval($payment['amount'] ?? 0);
                }
            }
        }

        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $status = 'Unpaid';
        if ($receivedAmount >= $grandTotal && $grandTotal > 0) {
            $status = 'Paid';
        } elseif ($receivedAmount > 0 && $receivedAmount < $grandTotal) {
            $status = 'Partial';
        }

        $sale->update([
            'party_name' => $data['party_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? $sale->bill_number,
            'invoice_date' => $data['invoice_date'] ?? $sale->invoice_date,
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $grandTotal,
            'received_amount' => $receivedAmount,
            'balance' => $balance,
            'status' => $status,
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ]);

        // Replace items (keep payment history intact for edit mode)
        $sale->items()->delete();
        foreach ($data['items'] as $item) {
            $sale->items()->create([
                'item_name' => $item['item_name'] ?? null,
                'item_category' => $item['item_category'] ?? null,
                'item_code' => $item['item_code'] ?? null,
                'item_description' => $item['item_description'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'amount' => $item['amount'] ?? 0,
            ]);
        }

        // Append new payments (maintain payment history; edit mode treats payments as additional amounts)
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                // Skip empty or incomplete payment entries
                if (empty($payment['bank_account_id']) || empty($payment['amount'])) {
                    continue;
                }

                $sale->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);

                if (!empty($payment['bank_account_id']) && !empty($payment['amount'])) {
                    $bank = BankAccount::find($payment['bank_account_id']);
                    if ($bank) {
                        $bank->opening_balance = ($bank->opening_balance ?? 0) + floatval($payment['amount']);
                        $bank->save();
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('sale.index'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'party_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.payment_type' => 'required|string|max:50',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        $receivedAmount = 0;

        // Calculate received amount from payments (only bank payments count as received)
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                if (!empty($payment['bank_account_id'])) {
                    $receivedAmount += floatval($payment['amount'] ?? 0);
                }
            }
        }

        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $status = 'Unpaid';
        if ($receivedAmount >= $grandTotal && $grandTotal > 0) {
            $status = 'Paid';
        } elseif ($receivedAmount > 0 && $receivedAmount < $grandTotal) {
            $status = 'Partial';
        }

        $sale = Sale::create([
            'party_name' => $data['party_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? null,
            'invoice_date' => $data['invoice_date'] ?? now(),
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $grandTotal,
            'received_amount' => $receivedAmount,
            'balance' => $balance,
            'status' => $status,
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ]);

        // Auto-generate invoice number based on the sale ID if not provided
        if (empty($sale->bill_number)) {
            $sale->bill_number = (string) $sale->id;
            $sale->save();
        }

        foreach ($data['items'] as $item) {
            $sale->items()->create([
                'item_name' => $item['item_name'] ?? null,
                'item_category' => $item['item_category'] ?? null,
                'item_code' => $item['item_code'] ?? null,
                'item_description' => $item['item_description'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'amount' => $item['amount'] ?? 0,
            ]);
        }

        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                $sale->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);

                // If payment is for a bank account, update the opening balance
                if (!empty($payment['bank_account_id']) && !empty($payment['amount'])) {
                    $bank = BankAccount::find($payment['bank_account_id']);
                    if ($bank) {
                        $bank->opening_balance = ($bank->opening_balance ?? 0) + floatval($payment['amount']);
                        $bank->save();
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('sale.index'),
        ]);
    }

    public function estimate()
    {
        return view('dashboard.sales.estimate');
    }

    public function pos()
    {
        return view('dashboard.sales.pos');
    }

    public function destroy(Sale $sale)
    {
        // Remove related items and payments first to avoid foreign key issues
        $sale->items()->delete();
        $sale->payments()->delete();

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully.',
        ]);
    }


}

