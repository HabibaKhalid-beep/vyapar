<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BrokerController extends Controller
{
    public function index(): View
    {
        $brokers = Broker::query()
            ->latest()
            ->get();

        $brokerSalesTotals = $this->buildBrokerSalesTotalsMap();

        $brokers->each(function (Broker $broker) use ($brokerSalesTotals) {
            $brokerSalesTotal = (float) ($brokerSalesTotals[$broker->id] ?? 0);
            $broker->setAttribute('broker_sales_total', $brokerSalesTotal);
            $broker->setAttribute('broker_summary_total', (float) ($broker->total_brokerage ?? 0) + $brokerSalesTotal);
        });

        $salesTypes = Sale::query()
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values();

        $metrics = [
            'total_brokers' => $brokers->count(),
            'active_brokers' => $brokers->where('status', true)->count(),
            'total_brokerage' => (float) $brokers->sum('total_brokerage'),
            'remaining_brokerage' => (float) $brokers->sum(function (Broker $broker) {
                return (float) ($broker->remaining_brokerage ?? $broker->remaining_amount);
            }),
        ];

        return view('brokers.index', compact('brokers', 'metrics', 'salesTypes'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validateBroker($request);

        $broker = Broker::create($data);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'broker' => $broker,
            ]);
        }

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker added successfully.');
    }

    public function update(Request $request, Broker $broker): RedirectResponse|JsonResponse
    {
        $data = $this->validateBroker($request);

        $broker->update($data);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'broker' => $broker->fresh()->loadSum('sales as broker_sales_total', 'broker_amount'),
            ]);
        }

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker updated successfully.');
    }

    public function destroy(Broker $broker): RedirectResponse|JsonResponse
    {
        $broker->delete();

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
            ]);
        }

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker deleted successfully.');
    }

    public function history(Request $request, Broker $broker): JsonResponse
    {
        $query = Sale::query()
            ->with(['party', 'details'])
            ->whereNotNull('invoice_date');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('from')) {
            $query->whereDate('invoice_date', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('invoice_date', '<=', $request->input('to'));
        }

        if ($request->filled('brokerage')) {
            if ($request->input('brokerage') === 'yes') {
                $query->where('broker_amount', '>', 0);
            } elseif ($request->input('brokerage') === 'no') {
                $query->where('broker_amount', '=', 0);
            }
        }

        $sales = $query->latest('invoice_date')->limit(300)->get()
            ->map(function (Sale $sale) use ($broker) {
            $brokerAmount = (float) ($this->resolveBrokerAmountsForSale($sale)[$broker->id] ?? 0);
            if ($brokerAmount <= 0) {
                return null;
            }
            $netAmount = max(0, ((float) $sale->grand_total) - $brokerAmount);

            return [
                'id' => $sale->id,
                'type' => $sale->type,
                'bill_number' => $sale->bill_number,
                'reference_bill_number' => $sale->reference_bill_number,
                'invoice_date' => optional($sale->invoice_date)->format('Y-m-d'),
                'party_name' => $sale->party?->name ?? '-',
                'total_amount' => (float) $sale->total_amount,
                'brokerage_type' => $sale->brokerage_type,
                'brokerage_rate' => (float) $sale->brokerage_rate,
                'broker_amount' => $brokerAmount,
                'net_amount' => $netAmount,
                'status' => $sale->status,
            ];
        })->filter()->values();

        return response()->json(['sales' => $sales]);
    }

    private function buildBrokerSalesTotalsMap(): array
    {
        $totals = [];

        Sale::query()
            ->with('details')
            ->select(['id', 'broker_id', 'broker_amount'])
            ->chunkById(200, function (Collection $sales) use (&$totals) {
                foreach ($sales as $sale) {
                    foreach ($this->resolveBrokerAmountsForSale($sale) as $brokerId => $amount) {
                        if ($brokerId <= 0 || $amount <= 0) {
                            continue;
                        }

                        $totals[$brokerId] = round(($totals[$brokerId] ?? 0) + $amount, 2);
                    }
                }
            });

        return $totals;
    }

    private function resolveBrokerAmountsForSale(Sale $sale): array
    {
        $details = $sale->details;
        $rows = is_array($details?->custom_expenses) ? $details->custom_expenses : [];
        $totals = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $accountType = strtolower(trim((string) ($row['account_type'] ?? '')));
            $brokerId = (int) ($row['account_id'] ?? $row['brokerId'] ?? 0);
            $amount = (float) ($row['amount'] ?? $row['value'] ?? 0);

            if ($accountType !== 'broker' || $brokerId <= 0 || $amount <= 0) {
                continue;
            }

            $totals[$brokerId] = round(($totals[$brokerId] ?? 0) + $amount, 2);
        }

        if (!empty($totals)) {
            return $totals;
        }

        $fallbackBrokerId = (int) ($sale->broker_id ?? 0);
        $fallbackAmount = round((float) ($sale->broker_amount ?? 0), 2);

        if ($fallbackBrokerId > 0 && $fallbackAmount > 0) {
            return [$fallbackBrokerId => $fallbackAmount];
        }

        return [];
    }

    private function validateBroker(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'commission_type' => 'required|in:percent,fixed',
            'commission_rate' => 'nullable|numeric|min:0|max:99999999.99',
            'total_brokerage' => 'nullable|numeric|min:0|max:999999999999.99',
            'paid_brokerage' => 'nullable|numeric|min:0|lte:total_brokerage|max:999999999999.99',
            'notes' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data['commission_rate'] = $data['commission_rate'] ?? 0;
        $data['total_brokerage'] = $data['total_brokerage'] ?? 0;
        $data['paid_brokerage'] = $data['paid_brokerage'] ?? 0;
        $data['status'] = $request->boolean('status');

        return $data;
    }
}
