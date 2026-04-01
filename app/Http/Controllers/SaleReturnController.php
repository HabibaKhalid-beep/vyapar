<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    public function saleReturn(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $saleReturns = Sale::with(['items', 'payments', 'party'])
            ->where('type', 'sale_return')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('bill_number', 'like', '%' . $search . '%')
                        ->orWhereHas('party', function ($partyQuery) use ($search) {
                            $partyQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.sales.sale-return', compact('saleReturns', 'search'));
    }

    public function salereturncreate()
    {
        return $this->renderSaleReturnForm();
    }

    public function edit(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);

        $sale->load(['items', 'payments']);

        return $this->renderSaleReturnForm($sale);
    }

    public function duplicate(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);

        $sale->load(['items', 'payments']);

        return $this->renderSaleReturnForm(null, $sale);
    }

    public function store(Request $request)
    {
        $data = $this->validateSaleReturnRequest($request);
        $receivedAmount = $this->calculateReceivedAmount($data['payments'] ?? []);
        $grandTotal = (float) ($data['grand_total'] ?? 0);

        $sale = Sale::create($this->buildSalePayload(
            $data,
            $receivedAmount,
            max(0, $grandTotal - $receivedAmount),
            $this->resolvePaymentStatus($receivedAmount, $grandTotal)
        ));

        $this->syncItems($sale, $data['items']);
        $this->syncPayments($sale, $data['payments'] ?? []);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('sale-return'),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);

        $data = $this->validateSaleReturnRequest($request);
        $existingReceived = (float) ($sale->received_amount ?? 0);
        $newReceived = $this->calculateReceivedAmount($data['payments'] ?? []);
        $receivedAmount = $existingReceived + $newReceived;
        $grandTotal = (float) ($data['grand_total'] ?? 0);

        $sale->update($this->buildSalePayload(
            $data,
            $receivedAmount,
            max(0, $grandTotal - $receivedAmount),
            $this->resolvePaymentStatus($receivedAmount, $grandTotal)
        ));

        $sale->items()->delete();
        $this->syncItems($sale, $data['items']);
        $this->syncPayments($sale, $data['payments'] ?? []);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('sale-return'),
        ]);
    }

    public function destroy(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);

        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale return deleted successfully.',
        ]);
    }

    public function preview(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);
        $sale->load(['items', 'payments', 'party']);

        return view('dashboard.sales.sale-return-preview', compact('sale'));
    }

    public function print(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);
        $sale->load(['items', 'payments', 'party']);

        return view('dashboard.sales.sale-return-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdf(Sale $sale)
    {
        abort_unless($sale->type === 'sale_return', 404);
        $sale->load(['items', 'payments', 'party']);

        return view('dashboard.sales.sale-return-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    private function renderSaleReturnForm(?Sale $saleReturn = null, ?Sale $duplicateSaleReturn = null)
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'SR-' . str_pad((string) $nextSaleId, 4, '0', STR_PAD_LEFT);

        return view('dashboard.sales.create-sale-return', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'saleReturn',
            'duplicateSaleReturn'
        ));
    }

    private function validateSaleReturnRequest(Request $request): array
    {
        return $request->validate([
            'party_id' => 'nullable|exists:parties,id',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'required|string|max:100',
            'invoice_date' => 'nullable|date',
            'order_date' => 'nullable|date',
            'due_date' => 'nullable|date',
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
    }

    private function buildSalePayload(array $data, float $receivedAmount, float $balance, string $status): array
    {
        return [
            'type' => 'sale_return',
            'party_id' => $data['party_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'order_date' => $data['order_date'] ?? ($data['invoice_date'] ?? now()->toDateString()),
            'due_date' => $data['due_date'] ?? ($data['order_date'] ?? $data['invoice_date'] ?? now()->toDateString()),
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'received_amount' => $receivedAmount,
            'balance' => $balance,
            'status' => $status,
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ];
    }

    private function syncItems(Sale $sale, array $items): void
    {
        foreach ($items as $item) {
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
    }

    private function syncPayments(Sale $sale, array $payments): void
    {
        foreach ($payments as $payment) {
            if (empty($payment['bank_account_id']) || empty($payment['amount'])) {
                continue;
            }

            $sale->payments()->create([
                'payment_type' => $payment['payment_type'],
                'bank_account_id' => $payment['bank_account_id'],
                'amount' => $payment['amount'],
                'reference' => $payment['reference'] ?? null,
            ]);

            $bank = BankAccount::find($payment['bank_account_id']);
            if ($bank) {
                $bank->opening_balance = ($bank->opening_balance ?? 0) + (float) $payment['amount'];
                $bank->save();
            }
        }
    }

    private function calculateReceivedAmount(array $payments): float
    {
        $receivedAmount = 0;

        foreach ($payments as $payment) {
            if (!empty($payment['bank_account_id'])) {
                $receivedAmount += (float) ($payment['amount'] ?? 0);
            }
        }

        return $receivedAmount;
    }

    private function resolvePaymentStatus(float $receivedAmount, float $grandTotal): string
    {
        if ($receivedAmount >= $grandTotal && $grandTotal > 0) {
            return 'Paid';
        }

        if ($receivedAmount > 0 && $receivedAmount < $grandTotal) {
            return 'Partial';
        }

        return 'Unpaid';
    }
}
