<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PartyController extends Controller
{
    // Display all parties
    public function index()
    {
        Party::query()->select('id')->orderBy('id')->chunk(200, function ($parties) {
            foreach ($parties as $party) {
                $this->syncPartyCurrentBalance((int) $party->id);
            }
        });

        $parties = Party::with('sales')->latest()->get();
        return view('parties.index', compact('parties'));
    }

    // Show create party form
    public function create(Request $request)
    {
        $returnUrl = $request->query('return_url');
        return view('parties.create', compact('returnUrl'));
    }

    // Store a new party
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
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
            'due_days' => 'nullable|integer|min:1|max:100',
            'custom_fields' => 'nullable|array',
            'transaction_type' => 'nullable|in:receive,pay',
            'party_type' => 'nullable|array',
            'party_type.*' => 'in:customer,supplier',
            'party_group' => 'nullable|string|max:100',
        ]);
        $data['party_type'] = $this->normalizePartyType($data['party_type'] ?? []);
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
    'debit'    => $request->input('transaction_type') === 'receive' ? ($request->input('opening_balance') ?? 0) : 0,
    'credit'   => $request->input('transaction_type') === 'pay' ? ($request->input('opening_balance') ?? 0) : 0,
    'balance'  => $request->input('opening_balance') ?? 0,
    'running_balance' => $request->input('opening_balance') ?? 0,
    'status'   => $request->input('transaction_type') ?? 'unpaid',
]);

        $this->syncPartyCurrentBalance($party->id);

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
        'phone_number_2' => 'sometimes|nullable|string|max:20',
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
        'due_days' => 'sometimes|nullable|integer|min:1|max:100',
        'custom_fields' => 'sometimes|nullable|array',
        'transaction_type' => 'sometimes|nullable|in:receive,pay',
        'party_type' => 'sometimes|nullable|array',
        'party_type.*' => 'in:customer,supplier',
        'party_group' => 'sometimes|nullable|string|max:100',
    ]);
    if (array_key_exists('party_type', $data)) {
        $data['party_type'] = $this->normalizePartyType($data['party_type'] ?? []);
    }
    if (array_key_exists('credit_limit_enabled', $data) && empty($data['credit_limit_enabled'])) {
        $data['credit_limit_amount'] = null;
    }

    $party->update($data);

    // ✅ Transaction update
    $openingTransaction = Transaction::query()
        ->where('party_id', $party->id)
        ->whereNull('transfer_group')
        ->where(function ($query) {
            $query->whereIn('type', ['receive', 'pay'])
                ->orWhere('number', 'like', 'TXN%');
        })
        ->orderBy('id')
        ->first();

    if ($openingTransaction) {
        $openingTransaction->update([
            'type' => $request->input('transaction_type', $openingTransaction->type),
            'date' => $request->input('as_of_date', $openingTransaction->date),
            'total' => $request->input('opening_balance', $openingTransaction->total),
            'debit' => $request->input('transaction_type', $openingTransaction->type) === 'receive'
                ? $request->input('opening_balance', $openingTransaction->total)
                : 0,
            'credit' => $request->input('transaction_type', $openingTransaction->type) === 'pay'
                ? $request->input('opening_balance', $openingTransaction->total)
                : 0,
            'balance' => $request->input('opening_balance', $openingTransaction->balance),
            'running_balance' => $request->input('opening_balance', $openingTransaction->running_balance ?? $openingTransaction->balance),
            'status' => $request->input('transaction_type', $openingTransaction->status),
        ]);
    } elseif ($request->filled('opening_balance') || $request->filled('transaction_type')) {
        Transaction::create([
            'party_id' => $party->id,
            'type' => $request->input('transaction_type'),
            'number' => 'TXN' . time(),
            'date' => $request->input('as_of_date') ?? now(),
            'total' => $request->input('opening_balance') ?? 0,
            'debit' => $request->input('transaction_type') === 'receive' ? ($request->input('opening_balance') ?? 0) : 0,
            'credit' => $request->input('transaction_type') === 'pay' ? ($request->input('opening_balance') ?? 0) : 0,
            'balance' => $request->input('opening_balance') ?? 0,
            'running_balance' => $request->input('opening_balance') ?? 0,
            'status' => $request->input('transaction_type') ?? 'unpaid',
        ]);
    }

    $this->syncPartyCurrentBalance($party->id);

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

    private function normalizePartyType(array $partyTypes): ?string
    {
        $allowedTypes = ['customer', 'supplier'];

        $normalizedTypes = collect($partyTypes)
            ->filter(fn ($type) => in_array($type, $allowedTypes, true))
            ->unique()
            ->values()
            ->all();

        return $normalizedTypes ? implode(',', $normalizedTypes) : null;
    }

public function transactions(Party $party)
{
    $this->syncPartyCurrentBalance($party->id);
    $party->refresh();
    $party->loadMissing(['transactions.counterParty', 'sales', 'purchases']);

    $salesTransactions = Sale::query()
        ->where('party_id', $party->id)
        ->get()
        ->map(function ($sale) {
            $amount = (float) ($sale->grand_total ?? $sale->total_amount ?? 0);
            $date = $sale->invoice_date ?? $sale->order_date ?? $sale->due_date ?? $sale->created_at;
            $typeLabel = match ($sale->type) {
                'invoice', 'pos' => 'Sale',
                'sale_return' => 'Sale Return',
                'estimate' => 'Estimate',
                'sale_order' => 'Sale Order',
                'proforma' => 'Proforma Invoice',
                'delivery_challan' => 'Delivery Challan',
                default => ucfirst((string) $sale->type),
            };

            $effect = match ($sale->type) {
                'sale_return' => -1 * $amount,
                'invoice', 'pos' => $amount,
                default => 0,
            };

            return [
                'id' => 'sale-' . $sale->id,
                'type' => $typeLabel,
                'raw_type' => (string) $sale->type,
                'source' => 'sale',
                'number' => $sale->bill_number ?: (string) $sale->id,
                'date' => optional($date),
                'description' => (string) ($sale->description ?? ''),
                'debit' => $effect > 0 ? $effect : 0,
                'credit' => $effect < 0 ? abs($effect) : 0,
                'effect' => $effect,
                'row_balance' => (float) ($sale->balance ?? max(0, $amount - (float) ($sale->received_amount ?? 0))),
                'due_date' => optional($sale->due_date),
                'status' => (string) ($sale->status ?? ''),
                'sort_order' => 20,
                'actions' => $this->saleActionUrls($sale),
            ];
        });

    $purchaseTransactions = Purchase::query()
        ->where('party_id', $party->id)
        ->get()
        ->map(function ($purchase) {
            $amount = (float) ($purchase->grand_total ?? $purchase->total_amount ?? 0);
            $date = $purchase->bill_date ?? $purchase->due_date ?? $purchase->created_at;
            $effect = $purchase->type === 'purchase_return' ? $amount : -1 * $amount;

            return [
                'id' => 'purchase-' . $purchase->id,
                'type' => $purchase->type === 'purchase_return' ? 'Purchase Return' : 'Purchase',
                'raw_type' => (string) $purchase->type,
                'source' => 'purchase',
                'number' => $purchase->bill_number ?: (string) $purchase->id,
                'date' => optional($date),
                'description' => (string) ($purchase->description ?? ''),
                'debit' => $effect > 0 ? $effect : 0,
                'credit' => $effect < 0 ? abs($effect) : 0,
                'effect' => $effect,
                'row_balance' => (float) ($purchase->balance ?? 0),
                'due_date' => optional($purchase->due_date),
                'status' => (string) ($purchase->balance > 0 ? 'unpaid' : 'paid'),
                'sort_order' => 30,
                'actions' => [],
            ];
        });

    $manualLedgerTransactions = $party->transactions
        ->whereNull('transfer_group')
        ->reject(function ($txn) {
            return in_array(strtolower((string) $txn->type), ['sale', 'sale_return', 'purchase', 'purchase_return', 'payment_in', 'payment_out'], true);
        })
        ->map(function ($txn) {
        $effect = $txn->ledgerEffectValue();

        return [
            'id' => 'txn-' . $txn->id,
            'type' => $this->formatLedgerTypeLabel((string) $txn->type),
            'raw_type' => (string) $txn->type,
            'source' => !empty($txn->transfer_group) ? 'transfer' : 'ledger',
            'number' => $txn->number ?: '-',
            'date' => optional($txn->date),
            'description' => (string) ($txn->description ?? ''),
            'debit' => $effect > 0 ? $effect : 0,
            'credit' => $effect < 0 ? abs($effect) : 0,
            'effect' => $effect,
            'row_balance' => (float) ($txn->total ?? 0),
            'due_date' => optional($txn->due_date),
            'status' => (string) ($txn->status ?? $txn->type ?? ''),
            'counter_party_name' => $txn->counterParty?->name,
            'sort_order' => in_array(strtolower((string) $txn->type), ['receive', 'pay'], true) ? 10 : 40,
            'actions' => [],
        ];
    });

    $transactions = $salesTransactions
        ->concat($purchaseTransactions)
        ->concat($manualLedgerTransactions)
        ->sortBy(function ($entry) {
            return sprintf(
                '%012d-%03d-%s',
                (int) ($entry['date']?->timestamp ?? 0),
                (int) ($entry['sort_order'] ?? 999),
                (string) $entry['id']
            );
        })
        ->values();

    $runningBalance = 0.0;
    $transactions = $transactions->map(function ($entry) use (&$runningBalance) {
        $runningBalance += (float) ($entry['effect'] ?? 0);
        $entry['date'] = $entry['date']?->format('d/m/Y');
        $entry['due_date'] = $entry['due_date']?->format('Y-m-d');
        $entry['total'] = number_format((float) (($entry['debit'] ?? 0) + ($entry['credit'] ?? 0)), 2);
        $entry['balance'] = number_format((float) ($entry['row_balance'] ?? 0), 2);
        $entry['debit'] = number_format((float) ($entry['debit'] ?? 0), 2);
        $entry['credit'] = number_format((float) ($entry['credit'] ?? 0), 2);
        $entry['running_balance'] = number_format($runningBalance, 2);
        unset($entry['effect'], $entry['row_balance'], $entry['sort_order']);
        return $entry;
    });

    return response()->json([
        'success' => true,
        'transactions' => $transactions->values(),
        'party_name' => $party->name,
        'total_balance' => number_format((float) $party->current_balance, 2),
        'overdue_transactions' => $transactions
            ->filter(fn ($entry) => (float) str_replace(',', '', $entry['running_balance']) > 0 && !empty($entry['due_date']) && $entry['due_date'] < now()->toDateString())
            ->values(),
    ]);
}

public function transferHistory(Party $party)
{
    $transfers = $party->transactions()
        ->with('counterParty')
        ->whereNotNull('transfer_group')
        ->orderByDesc('date')
        ->orderByDesc('id')
        ->get()
        ->map(function (Transaction $transaction) {
            return [
                'id' => $transaction->id,
                'date' => optional($transaction->date)?->format('d-M-Y'),
                'ref_no' => $transaction->number ?: '-',
                'type' => (string) $transaction->type,
                'counter_party' => $transaction->counterParty?->name ?: '-',
                'amount' => number_format((float) ($transaction->total ?? 0), 2),
                'description' => (string) ($transaction->description ?? '-'),
                'status' => (string) ($transaction->status ?? '-'),
            ];
        })
        ->values();

    return response()->json([
        'success' => true,
        'party_name' => $party->name,
        'transfers' => $transfers,
    ]);
}

public function ledger(Party $party)
{
    $party->loadMissing(['transactions.counterParty']);

    $runningBalance = 0.0;

    $ledger = $party->transactions()
        ->whereNull('transfer_group')
        ->whereIn('type', ['sale', 'sale_return', 'payment_in', 'payment_out', 'receive', 'pay'])
        ->orderBy('date')
        ->orderBy('id')
        ->get()
        ->map(function (Transaction $transaction) use (&$runningBalance) {
            $credit = (float) $transaction->ledgerCreditValue();
            $debit = (float) $transaction->ledgerDebitValue();
            $runningBalance += Transaction::normalizeLedgerAmount($debit);
            $runningBalance -= Transaction::normalizeLedgerAmount($credit);
            $runningBalance = Transaction::normalizeLedgerAmount($runningBalance);

            return [
                'id' => $transaction->id,
                'number' => $transaction->number ?: '-',
                'date' => optional($transaction->date)?->format('d-M-Y'),
                'type' => $this->formatLedgerTypeLabel((string) $transaction->type),
                'description' => (string) ($transaction->description ?: ($transaction->counterParty?->name ? 'Counter Party: ' . $transaction->counterParty->name : '')),
                'credit' => number_format($credit, 2),
                'debit' => number_format($debit, 2),
                'balance' => number_format($runningBalance, 2),
                'running_balance' => number_format($runningBalance, 2),
            ];
        })
        ->values();

    return response()->json([
        'success' => true,
        'party_name' => $party->name,
        'ledger' => $ledger,
    ]);
}

private function formatLedgerTypeLabel(string $type): string
{
    return match (strtolower($type)) {
        'pay' => 'Payable Opening Balance',
        'receive' => 'Receivable Opening Balance',
        'payment_in' => 'Payment In',
        'payment_out' => 'Payment Out',
        'party to party[received]' => 'Party to Party[Received]',
        'party to party[paid]' => 'Party to Party[Paid]',
        default => ucwords(str_replace(['_', '-'], ' ', $type)),
    };
}

private function buildLedgerSourceKey(string $type, string $number): string
{
    return strtolower(trim($type)) . '|' . strtolower(trim($number));
}

private function saleActionUrls(Sale $sale): array
{
    $reactPreviewUrl = route('invoice', ['sale_id' => $sale->id]);
    $reactPdfUrl = route('invoice', ['sale_id' => $sale->id]);
    $reactPrintUrl = route('invoice', ['sale_id' => $sale->id, 'print' => 1]);
    $reactDeliveryPreviewUrl = route('invoice', ['sale_id' => $sale->id, 'doc' => 'delivery_challan']);
    $reactDeliveryPrintUrl = $reactDeliveryPreviewUrl . '&print=1';
    $reactProformaPreviewUrl = route('proforma-invoice.react', $sale);
    $reactProformaPdfUrl = $reactProformaPreviewUrl;
    $reactProformaPrintUrl = $reactProformaPreviewUrl . '?print=1';

    return match ($sale->type) {
        'invoice', 'pos' => [
            'view' => route('sale.edit', $sale),
            'delete' => route('sale.destroy', $sale),
            'cancel' => route('sale.cancel', $sale),
            'duplicate' => route('sale.create', ['type' => $sale->type === 'pos' ? 'pos' : 'invoice']) . '?duplicate_sale_id=' . $sale->id,
            'pdf' => $reactPdfUrl,
            'preview' => $reactPreviewUrl,
            'print' => $reactPrintUrl,
            'preview_delivery' => $reactDeliveryPreviewUrl,
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
            'pdf' => $reactProformaPdfUrl,
            'preview' => $reactProformaPreviewUrl,
            'print' => $reactProformaPrintUrl,
            'preview_delivery' => null,
            'convert_return' => null,
            'history' => null,
        ],
        'delivery_challan' => [
            'view' => route('delivery-challan.edit', $sale),
            'delete' => route('delivery-challan.destroy', $sale),
            'cancel' => null,
            'duplicate' => route('delivery-challan.duplicate', $sale),
            'pdf' => $reactDeliveryPreviewUrl,
            'preview' => $reactDeliveryPreviewUrl,
            'print' => $reactDeliveryPrintUrl,
            'preview_delivery' => $reactDeliveryPreviewUrl,
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
}

public function storeTransfer(Request $request)
{
    if (is_string($request->input('rows'))) {
        $request->merge([
            'rows' => json_decode($request->input('rows'), true) ?? [],
        ]);
    }

    $data = $request->validate([
        'transfer_date' => 'required|date',
        'description' => 'nullable|string',
        'attachment' => 'nullable|image|max:4096',
        'rows' => 'required|array|size:2',
        'rows.*.party_id' => 'required|exists:parties,id',
        'rows.*.type' => 'required|in:received,paid',
        'rows.*.amount' => 'required|numeric|min:0.01',
    ]);
    $paidRow = collect($data['rows'])->firstWhere('type', 'paid');
    $receivedRow = collect($data['rows'])->firstWhere('type', 'received');

    if (!$paidRow || !$receivedRow) {
        return response()->json([
            'success' => false,
            'message' => 'One paid row and one received row are required.',
        ], 422);
    }

    if ((int) $paidRow['party_id'] === (int) $receivedRow['party_id']) {
        return response()->json([
            'success' => false,
            'message' => 'Paid party and received party cannot be same.',
        ], 422);
    }

    if ((float) $paidRow['amount'] !== (float) $receivedRow['amount']) {
        return response()->json([
            'success' => false,
            'message' => 'Paid and received amount must be equal.',
        ], 422);
    }

    $sourceParty = Party::findOrFail($paidRow['party_id']);
    $targetParty = Party::findOrFail($receivedRow['party_id']);
    $amount = (float) $paidRow['amount'];
    $transferGroup = 'PTP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
    $savedRows = [];
    $attachmentPath = $request->hasFile('attachment')
        ? $request->file('attachment')->store('party-transfers', 'public')
        : null;

    DB::transaction(function () use ($data, $sourceParty, $targetParty, $amount, $transferGroup, $attachmentPath, &$savedRows) {
        $description = $data['description'] ?? null;
        $number = $transferGroup . '-1';

        $targetTransaction = Transaction::create([
            'party_id' => $targetParty->id,
            'counter_party_id' => $sourceParty->id,
            'type' => 'Party to Party[Received]',
            'number' => $number,
            'transfer_group' => $transferGroup,
            'date' => $data['transfer_date'],
            'total' => $amount,
            'debit' => $amount,
            'credit' => 0,
            'balance' => $amount,
            'running_balance' => $amount,
            'status' => 'receive',
            'description' => $description,
            'attachment' => $attachmentPath,
        ]);

        $sourceTransaction = Transaction::create([
            'party_id' => $sourceParty->id,
            'counter_party_id' => $targetParty->id,
            'type' => 'Party to Party[Paid]',
            'number' => $number,
            'transfer_group' => $transferGroup,
            'date' => $data['transfer_date'],
            'total' => $amount,
            'debit' => 0,
            'credit' => $amount,
            'balance' => $amount,
            'running_balance' => $amount,
            'status' => 'pay',
            'description' => $description,
            'attachment' => $attachmentPath,
        ]);

        $savedRows[] = [
            'row' => 1,
            'source_transaction_id' => $sourceTransaction->id,
            'target_transaction_id' => $targetTransaction->id,
            'paid_party_name' => $sourceParty->name,
            'received_party_name' => $targetParty->name,
            'amount' => number_format($amount, 2, '.', ''),
            'type' => 'cross_transfer',
        ];
    });

    $this->syncPartyCurrentBalance($sourceParty->id);
    $this->syncPartyCurrentBalance($targetParty->id);

    return response()->json([
        'success' => true,
        'message' => 'Party transfer saved successfully.',
        'transfer_group' => $transferGroup,
        'rows' => $savedRows,
    ]);
}

private function syncPartyCurrentBalance(int $partyId): void
{
    Transaction::syncPartyCurrentBalance($partyId);
}
}
