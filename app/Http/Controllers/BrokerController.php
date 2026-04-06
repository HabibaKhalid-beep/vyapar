<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrokerController extends Controller
{
    public function index(): View
    {
        $brokers = Broker::latest()->get();

        $metrics = [
            'total_brokers' => $brokers->count(),
            'active_brokers' => $brokers->where('status', true)->count(),
            'total_brokerage' => (float) $brokers->sum('total_brokerage'),
            'remaining_brokerage' => (float) $brokers->sum(function (Broker $broker) {
                return (float) ($broker->remaining_brokerage ?? $broker->remaining_amount);
            }),
        ];

        return view('brokers.index', compact('brokers', 'metrics'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBroker($request);

        Broker::create($data);

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker added successfully.');
    }

    public function update(Request $request, Broker $broker): RedirectResponse
    {
        $data = $this->validateBroker($request);

        $broker->update($data);

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker updated successfully.');
    }

    public function destroy(Broker $broker): RedirectResponse
    {
        $broker->delete();

        return redirect()
            ->route('brokers.index')
            ->with('success', 'Broker deleted successfully.');
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
