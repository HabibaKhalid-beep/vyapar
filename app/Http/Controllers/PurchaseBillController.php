<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseBillController extends Controller
{
    public function purchaseExpenses(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $purchases = Purchase::with(['items', 'payments.bankAccount', 'party'])
            ->where('type', 'purchase_bill')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('bill_number', 'like', '%' . $search . '%')
                        ->orWhere('party_name', 'like', '%' . $search . '%')
                        ->orWhereHas('party', function ($partyQuery) use ($search) {
                            $partyQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('bill_date')
            ->orderByDesc('created_at')
            ->get();

        $paidTotal = (float) $purchases->sum('paid_amount');
        $unpaidTotal = (float) $purchases->sum('balance');
        $grandTotal = (float) $purchases->sum('grand_total');

        return view('dashboard.purchases.purchase-bill', compact(
            'purchases',
            'search',
            'paidTotal',
            'unpaidTotal',
            'grandTotal'
        ));
    }

    public function create()
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextInvoiceNumber = 'PB-' . ((Purchase::max('id') ?? 0) + 1);
        $convertedPurchaseData = null;

        $sourcePurchaseOrderId = request()->integer('source_purchase_order_id');
        if ($sourcePurchaseOrderId) {
            $alreadyConvertedPurchase = Purchase::where('type', 'purchase_bill')
                ->where('source_purchase_order_id', $sourcePurchaseOrderId)
                ->first();

            if ($alreadyConvertedPurchase) {
                return redirect()
                    ->route('purchase-order')
                    ->with('error', 'This purchase order is already converted to purchase #' . ($alreadyConvertedPurchase->bill_number ?? $alreadyConvertedPurchase->id));
            }

            $sourcePurchaseOrder = Purchase::with(['items', 'payments'])
                ->where('type', 'purchase_order')
                ->find($sourcePurchaseOrderId);

            if ($sourcePurchaseOrder) {
                $convertedPurchaseData = $sourcePurchaseOrder;
            }
        }

        return view('dashboard.purchases.create-purchase-bill', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedPurchaseData'
        ));
    }

    public function edit(Purchase $purchase)
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $purchase->load(['items', 'payments']);

        return view('dashboard.purchases.create-purchase-bill', compact(
            'bankAccounts',
            'items',
            'parties',
            'purchase'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatePurchase($request);
        $purchase = $this->savePurchase(new Purchase(), $data);

        return response()->json([
            'success' => true,
            'purchase_id' => $purchase->id,
            'bill_number' => $purchase->bill_number,
            'redirect_url' => route('purchase-expenses'),
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $this->validatePurchase($request);
        $purchase = $this->savePurchase($purchase, $data);

        return response()->json([
            'success' => true,
            'purchase_id' => $purchase->id,
            'bill_number' => $purchase->bill_number,
            'redirect_url' => route('purchase-expenses'),
        ]);
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->items()->delete();
        $purchase->payments()->delete();
        $purchase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase bill deleted successfully.',
        ]);
    }

    public function preview(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);

        return view('dashboard.purchases.purchase-preview', compact('purchase'));
    }

    public function print(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);

        return view('dashboard.purchases.purchase-preview', [
            'purchase' => $purchase,
            'autoPrint' => true,
        ]);
    }

    public function pdf(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);

        return view('dashboard.purchases.purchase-preview', [
            'purchase' => $purchase,
            'pdfMode' => true,
        ]);
    }

    private function validatePurchase(Request $request): array
    {
        return $request->validate([
            'source_purchase_order_id' => 'nullable|exists:purchases,id',
            'party_id' => 'nullable|exists:parties,id',
            'party_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'bill_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
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
    }

    private function savePurchase(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            if (!empty($data['source_purchase_order_id'])) {
                $existingConvertedPurchase = Purchase::where('type', 'purchase_bill')
                    ->where('source_purchase_order_id', $data['source_purchase_order_id'])
                    ->when($purchase->exists, function ($query) use ($purchase) {
                        $query->where('id', '!=', $purchase->id);
                    })
                    ->first();

                if ($existingConvertedPurchase) {
                    throw ValidationException::withMessages([
                        'source_purchase_order_id' => 'This purchase order is already converted to purchase #' . ($existingConvertedPurchase->bill_number ?? $existingConvertedPurchase->id),
                    ]);
                }
            }

            $purchase->fill([
                'type' => 'purchase_bill',
                'source_purchase_order_id' => $data['source_purchase_order_id'] ?? null,
                'party_id' => $data['party_id'] ?? null,
                'party_name' => $data['party_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'bill_number' => $data['bill_number'] ?? null,
                'bill_date' => $data['bill_date'] ?? now()->toDateString(),
                'total_qty' => $data['total_qty'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'discount_pct' => $data['discount_pct'] ?? 0,
                'discount_rs' => $data['discount_rs'] ?? 0,
                'tax_pct' => $data['tax_pct'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'shipping_charge' => $data['shipping_charge'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'balance' => $data['balance'] ?? 0,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'document_path' => $data['document_path'] ?? null,
            ]);

            $purchase->save();

            if (empty($purchase->bill_number)) {
                $purchase->bill_number = 'PB-' . $purchase->id;
                $purchase->save();
            }

            $purchase->items()->delete();
            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'item_id' => $item['item_id'] ?? null,
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

            $purchase->payments()->delete();
            foreach ($data['payments'] ?? [] as $payment) {
                $purchase->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'] ?? 0,
                    'reference' => $payment['reference'] ?? null,
                ]);
            }

            return $purchase->fresh(['items', 'payments.bankAccount', 'party']);
        });
    }
}
