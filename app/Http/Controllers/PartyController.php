<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Sale;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PartyController extends Controller
{
    // Display all parties
    public function index()
    {
        $parties = Party::with('sales')->latest()->get();
        return view('parties.index', compact('parties'));
    }

    // Store a new party
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'ptcl_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'credit_limit_enabled' => 'nullable|boolean',
            'credit_limit_amount' => 'nullable|numeric|min:0|required_if:credit_limit_enabled,1',
            'custom_fields' => 'nullable|array',
            'transaction_type' => 'nullable|in:receive,pay',
            'party_type' => 'nullable|in:customer,supplier',
            'party_group' => 'nullable|string|max:100',
        ]);
        if (empty($data['credit_limit_enabled'])) {
            $data['credit_limit_amount'] = null;
        }
      

// Party create ke baad

        $party = Party::create($data);
        $transaction = Transaction::create([
    'party_id' => $party->id,
    'type'     => $request->input('transaction_type'), // receive/pay
    'number'   => 'TXN' . time(), 
    'date'     => $request->input('as_of_date') ?? now(),
    'total'    => $request->input('opening_balance') ?? 0,
    'balance'  => $request->input('opening_balance') ?? 0,
    'status'   => $request->input('transaction_type') ?? 'unpaid',
]);


        $party->load('sales');

        return response()->json([
            'success' => true,
            'party' => $party
        ]);
 
        
    }

    // Show single party
  public function show($id)
{
    // Eager load transactions
    $party = Party::with(['transactions', 'sales'])->findOrFail($id);

    // Format transactions
    $transactions = $party->transactions
        ->sortByDesc('date')
        ->map(function ($txn) {
            return [
                'id'      => $txn->id,
                'type'    => $txn->type,
                'number'  => $txn->number,
                'date'    => $txn->date->format('d/m/Y'),
                'total'   => number_format($txn->total, 2),
                'balance' => number_format($txn->balance, 2),
                'status'  => $txn->status,
            ];
        });

    return response()->json([
        'success'       => true,
        'party'         => $party,
        'transactions'  => $transactions,
        'total_balance' => number_format((float) $party->current_balance, 2),
    ]);
}

    // Update existing party
public function update(Request $request, $id)
{
    $party = Party::findOrFail($id);

    $data = $request->validate([
        'name' => 'sometimes|string|max:255',
        'phone' => 'sometimes|nullable|string|max:20',
        'ptcl_number' => 'sometimes|nullable|string|max:30',
        'email' => 'sometimes|nullable|email|max:255',
        'city' => 'sometimes|nullable|string|max:100',
        'address' => 'sometimes|nullable|string|max:1000',
        'billing_address' => 'sometimes|nullable|string',
        'shipping_address' => 'sometimes|nullable|string',
        'opening_balance' => 'sometimes|nullable|numeric',
        'as_of_date' => 'sometimes|nullable|date',
        'credit_limit_enabled' => 'sometimes|nullable|boolean',
        'credit_limit_amount' => 'sometimes|nullable|numeric|min:0|required_if:credit_limit_enabled,1',
        'custom_fields' => 'sometimes|nullable|array',
        'transaction_type' => 'sometimes|nullable|in:receive,pay',
        'party_type' => 'sometimes|nullable|in:customer,supplier',
        'party_group' => 'sometimes|nullable|string|max:100',
    ]);
    if (array_key_exists('credit_limit_enabled', $data) && empty($data['credit_limit_enabled'])) {
        $data['credit_limit_amount'] = null;
    }

    $party->update($data);

    // ✅ Transaction update
    $transaction = $party->transactions()->latest()->first(); // last transaction
    if ($transaction) {
        $transaction->update([
            'type'    => $request->input('transaction_type', $transaction->type),
            'date'    => $request->input('as_of_date', $transaction->date),
            'total'   => $request->input('opening_balance', $transaction->total),
            'balance' => $request->input('opening_balance', $transaction->balance),
            'status'  => $request->input('transaction_type', $transaction->status),
        ]);
    }

    $party->load(['transactions', 'sales']);

    $transactions = $party->transactions
        ->sortByDesc('date')
        ->map(function ($txn) {
            return [
                'id'      => $txn->id,
                'type'    => $txn->type,
                'number'  => $txn->number,
                'date'    => $txn->date->format('d/m/Y'),
                'total'   => number_format($txn->total, 2),
                'balance' => number_format($txn->balance, 2),
                'status'  => $txn->status,
            ];
        });

    return response()->json([
        'success'       => true,
        'party'         => $party,
        'transactions'  => $transactions,
        'total_balance' => number_format((float) $party->current_balance, 2),
    ]);
}
    // Delete party
    public function destroy($id)
    {
        $party = Party::findOrFail($id);
        $party->delete();

        return response()->json(['success' => true]);
    }

public function transactions(Party $party)
{
    $party->loadMissing(['transactions', 'sales']);

    $salesTransactions = Sale::query()
        ->where('party_id', $party->id)
        ->get()
        ->map(function ($sale) {
            $typeLabel = match ($sale->type) {
                'invoice' => 'Sale',
                'estimate' => 'Estimate',
                'sale_order' => 'Sale Order',
                'proforma' => 'Proforma Invoice',
                'delivery_challan' => 'Delivery Challan',
                'sale_return' => 'Credit Note',
                'pos' => 'POS',
                default => ucfirst((string) $sale->type),
            };

            $date = $sale->invoice_date ?? $sale->order_date ?? $sale->due_date ?? $sale->created_at;
            $actionUrls = match ($sale->type) {
                'invoice', 'pos' => [
                    'view' => route('sale.edit', $sale),
                    'delete' => route('sale.destroy', $sale),
                    'cancel' => route('sale.cancel', $sale),
                    'duplicate' => route('sale.create', ['type' => $sale->type === 'pos' ? 'pos' : 'invoice']) . '?duplicate_sale_id=' . $sale->id,
                    'pdf' => route('sale.invoice-pdf', $sale),
                    'preview' => route('sale.invoice-preview', $sale),
                    'print' => route('sale.invoice-pdf', $sale),
                    'preview_delivery' => route('sale.delivery-preview', $sale),
                    'convert_return' => route('sale-return.create', ['sale_id' => $sale->id]),
                    'history' => route('sale.bank-history', $sale),
                ],
                'estimate' => [
                    'view' => route('estimates.create') . '?edit_sale_id=' . $sale->id,
                    'delete' => route('estimates.destroy', $sale),
                    'cancel' => null,
                    'duplicate' => route('estimates.create') . '?duplicate_sale_id=' . $sale->id,
                    'pdf' => route('estimates.pdf', $sale),
                    'preview' => route('estimates.preview', $sale),
                    'print' => route('estimates.print', $sale),
                    'preview_delivery' => null,
                    'convert_return' => null,
                    'history' => null,
                ],
                'sale_return' => [
                    'view' => route('sale-return.edit', $sale),
                    'delete' => route('sale-return.destroy', $sale),
                    'cancel' => null,
                    'duplicate' => route('sale-return.duplicate', $sale),
                    'pdf' => route('sale-return.pdf', $sale),
                    'preview' => route('sale-return.preview', $sale),
                    'print' => route('sale-return.print', $sale),
                    'preview_delivery' => null,
                    'convert_return' => null,
                    'history' => null,
                ],
                'proforma' => [
                    'view' => route('proforma-invoice.edit', $sale),
                    'delete' => route('proforma-invoice.destroy', $sale),
                    'cancel' => null,
                    'duplicate' => route('proforma-invoice.duplicate', $sale),
                    'pdf' => route('proforma-invoice.pdf', $sale),
                    'preview' => route('proforma-invoice.preview', $sale),
                    'print' => route('proforma-invoice.print', $sale),
                    'preview_delivery' => null,
                    'convert_return' => null,
                    'history' => null,
                ],
                'delivery_challan' => [
                    'view' => route('delivery-challan.edit', $sale),
                    'delete' => route('delivery-challan.destroy', $sale),
                    'cancel' => null,
                    'duplicate' => route('delivery-challan.duplicate', $sale),
                    'pdf' => route('delivery-challan.pdf', $sale),
                    'preview' => route('delivery-challan.preview', $sale),
                    'print' => route('delivery-challan.print', $sale),
                    'preview_delivery' => route('delivery-challan.preview', $sale),
                    'convert_return' => null,
                    'history' => null,
                ],
                'sale_order' => [
                    'view' => route('sale-order.create') . '?edit_sale_id=' . $sale->id,
                    'delete' => null,
                    'cancel' => null,
                    'duplicate' => route('sale-order.create') . '?duplicate_sale_id=' . $sale->id,
                    'pdf' => route('sale-orders.pdf', $sale),
                    'preview' => route('sale-orders.preview', $sale),
                    'print' => route('sale-orders.print', $sale),
                    'preview_delivery' => null,
                    'convert_return' => null,
                    'history' => null,
                ],
                default => [
                    'view' => route('sale.edit', $sale),
                    'delete' => route('sale.destroy', $sale),
                    'cancel' => null,
                    'duplicate' => null,
                    'pdf' => null,
                    'preview' => null,
                    'print' => null,
                    'preview_delivery' => null,
                    'convert_return' => null,
                    'history' => null,
                ],
            };

            return [
                'id' => $sale->id,
                'type' => $typeLabel,
                'raw_type' => (string) $sale->type,
                'source' => 'sale',
                'number' => $sale->bill_number ?: (string) $sale->id,
                'date' => optional($date)->format('d/m/Y'),
                'total' => number_format((float) ($sale->grand_total ?? $sale->total_amount ?? 0), 2),
                'balance' => number_format((float) ($sale->balance ?? 0), 2),
                'status' => (string) ($sale->status ?? ''),
                'actions' => $actionUrls,
                'sort_date' => optional($date)->timestamp ?? $sale->created_at?->timestamp ?? 0,
            ];
        });

    $openingBalanceTransactions = $party->transactions->map(function ($txn) {
        $typeLabel = match ((string) $txn->type) {
            'pay' => 'Payable Opening Balance',
            'receive' => 'Receivable Opening Balance',
            default => (string) $txn->type,
        };

        return [
            'id' => 'opening-' . $txn->id,
            'type' => $typeLabel,
            'raw_type' => (string) $txn->type,
            'source' => !empty($txn->transfer_group) ? 'transfer' : 'opening',
            'number' => $txn->number ?: '-',
            'date' => optional($txn->date)->format('d/m/Y'),
            'total' => number_format((float) ($txn->total ?? 0), 2),
            'balance' => number_format((float) ($txn->balance ?? 0), 2),
            'status' => (string) ($txn->status ?? $txn->type ?? ''),
            'counter_party_name' => $txn->counterParty?->name,
            'actions' => [],
            'sort_date' => optional($txn->date)->timestamp ?? 0,
        ];
    });

    $transactions = $salesTransactions
        ->concat($openingBalanceTransactions)
        ->sortByDesc('sort_date')
        ->values()
        ->map(function ($transaction) {
            unset($transaction['sort_date']);
            return $transaction;
        });

    return response()->json([
        'success'        => true,
        'transactions'   => $transactions,
        'party_name'     => $party->name,
        'total_balance'  => number_format((float) $party->current_balance, 2),
    ]);
}

public function storeTransfer(Request $request)
{
    $data = $request->validate([
        'source_party_id' => 'required|exists:parties,id',
        'transfer_date' => 'required|date',
        'description' => 'nullable|string',
        'rows' => 'required|array|min:1',
        'rows.*.party_id' => 'required|exists:parties,id|different:source_party_id',
        'rows.*.type' => 'required|in:received,paid',
        'rows.*.amount' => 'required|numeric|min:0.01',
    ]);

    $sourceParty = Party::findOrFail($data['source_party_id']);
    $transferGroup = 'PTP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
    $savedRows = [];

    DB::transaction(function () use ($data, $sourceParty, $transferGroup, &$savedRows) {
        foreach ($data['rows'] as $index => $row) {
            $targetParty = Party::findOrFail($row['party_id']);
            $amount = (float) $row['amount'];
            $isReceived = $row['type'] === 'received';

            $targetType = $isReceived ? 'Party to Party[Received]' : 'Party to Party[Paid]';
            $targetStatus = $isReceived ? 'receive' : 'pay';
            $sourceType = $isReceived ? 'Party to Party[Paid]' : 'Party to Party[Received]';
            $sourceStatus = $isReceived ? 'pay' : 'receive';
            $number = $transferGroup . '-' . ($index + 1);
            $description = $data['description'] ?? null;

            $targetTransaction = Transaction::create([
                'party_id' => $targetParty->id,
                'counter_party_id' => $sourceParty->id,
                'type' => $targetType,
                'number' => $number,
                'transfer_group' => $transferGroup,
                'date' => $data['transfer_date'],
                'total' => $amount,
                'balance' => $amount,
                'status' => $targetStatus,
                'description' => $description,
            ]);

            $sourceTransaction = Transaction::create([
                'party_id' => $sourceParty->id,
                'counter_party_id' => $targetParty->id,
                'type' => $sourceType,
                'number' => $number,
                'transfer_group' => $transferGroup,
                'date' => $data['transfer_date'],
                'total' => $amount,
                'balance' => $amount,
                'status' => $sourceStatus,
                'description' => $description,
            ]);

            $savedRows[] = [
                'row' => $index + 1,
                'source_transaction_id' => $sourceTransaction->id,
                'target_transaction_id' => $targetTransaction->id,
                'party_name' => $targetParty->name,
                'amount' => number_format($amount, 2, '.', ''),
                'type' => $row['type'],
            ];
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'Party transfer saved successfully.',
        'transfer_group' => $transferGroup,
        'rows' => $savedRows,
    ]);
}
}
