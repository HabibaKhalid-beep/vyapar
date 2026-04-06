<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Sale;
use App\Models\Purchase;

 class Party extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'ptcl_number',
        'email',
        'city',
        'address',
        'billing_address',
        'shipping_address',
        'opening_balance',
        'as_of_date',
        'credit_limit_enabled',
        'credit_limit_amount',
        'custom_fields',
        'transaction_type',
        'party_type',
        'party_group'
    ];

    protected $appends = [
        'current_balance',
        'formatted_current_balance',
    ];

    protected $casts = [
        'credit_limit_enabled' => 'boolean',
        'credit_limit_amount' => 'decimal:2',
        'custom_fields' => 'array',
        // ✅ 'date' cast hatao, neeche accessor use karo
    ];

    // ✅ ADD: as_of_date ko YYYY-MM-DD format mein return karo
    public function getAsOfDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }
     // ✅ Party ki saari transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('date', 'desc');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getCurrentBalanceAttribute()
    {
        $openingBalance = (float) ($this->opening_balance ?? 0);
        $transferReceivedTotal = $this->relationLoaded('transactions')
            ? (float) $this->transactions
                ->whereNotNull('transfer_group')
                ->where('status', 'receive')
                ->sum(fn ($transaction) => (float) ($transaction->balance ?? $transaction->total ?? 0))
            : (float) $this->transactions()
                ->whereNotNull('transfer_group')
                ->where('status', 'receive')
                ->get(['balance', 'total'])
                ->sum(fn ($transaction) => (float) ($transaction->balance ?? $transaction->total ?? 0));

        $transferPaidTotal = $this->relationLoaded('transactions')
            ? (float) $this->transactions
                ->whereNotNull('transfer_group')
                ->where('status', 'pay')
                ->sum(fn ($transaction) => (float) ($transaction->balance ?? $transaction->total ?? 0))
            : (float) $this->transactions()
                ->whereNotNull('transfer_group')
                ->where('status', 'pay')
                ->get(['balance', 'total'])
                ->sum(fn ($transaction) => (float) ($transaction->balance ?? $transaction->total ?? 0));

        $transferNet = $transferReceivedTotal - $transferPaidTotal;

        $salesTotal = $this->relationLoaded('sales')
            ? (float) $this->sales
                ->whereIn('type', ['invoice', 'pos'])
                ->sum(fn ($sale) => (float) ($sale->grand_total ?? $sale->total_amount ?? 0))
            : (float) $this->sales()
                ->whereIn('type', ['invoice', 'pos'])
                ->get(['grand_total', 'total_amount'])
                ->sum(fn ($sale) => (float) ($sale->grand_total ?? $sale->total_amount ?? 0));

        $saleReturnTotal = $this->relationLoaded('sales')
            ? (float) $this->sales
                ->where('type', 'sale_return')
                ->sum(fn ($sale) => (float) ($sale->grand_total ?? $sale->total_amount ?? 0))
            : (float) $this->sales()
                ->where('type', 'sale_return')
                ->get(['grand_total', 'total_amount'])
                ->sum(fn ($sale) => (float) ($sale->grand_total ?? $sale->total_amount ?? 0));

        $purchaseTotal = $this->relationLoaded('purchases')
            ? (float) $this->purchases
                ->where('type', 'purchase_bill')
                ->sum(fn ($purchase) => (float) ($purchase->grand_total ?? $purchase->total_amount ?? 0))
            : (float) $this->purchases()
                ->where('type', 'purchase_bill')
                ->get(['grand_total', 'total_amount'])
                ->sum(fn ($purchase) => (float) ($purchase->grand_total ?? $purchase->total_amount ?? 0));

        $purchaseReturnTotal = $this->relationLoaded('purchases')
            ? (float) $this->purchases
                ->where('type', 'purchase_return')
                ->sum(fn ($purchase) => (float) ($purchase->grand_total ?? $purchase->total_amount ?? 0))
            : (float) $this->purchases()
                ->where('type', 'purchase_return')
                ->get(['grand_total', 'total_amount'])
                ->sum(fn ($purchase) => (float) ($purchase->grand_total ?? $purchase->total_amount ?? 0));

        if ($this->transaction_type === 'pay') {
            return $openingBalance + $purchaseTotal - $purchaseReturnTotal + $transferNet;
        }

        if ($this->transaction_type === 'receive') {
            return $openingBalance + $salesTotal - $saleReturnTotal + $transferNet;
        }

        return $openingBalance + $transferNet;
    }

    public function getFormattedCurrentBalanceAttribute()
    {
        return number_format((float) $this->current_balance, 2);
    }

    public function paymentIns() {
    return $this->hasMany(PaymentIn::class);
}

}
