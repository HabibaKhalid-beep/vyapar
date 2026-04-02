<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;
use App\Models\Sale;

 class Party extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'opening_balance',
        'as_of_date',
        'credit_limit_enabled',
        'custom_fields',
        'transaction_type',
        'party_type'
    ];

    protected $appends = [
        'current_balance',
        'formatted_current_balance',
    ];

    protected $casts = [
        'credit_limit_enabled' => 'boolean',
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

    public function getCurrentBalanceAttribute()
    {
        $openingBalance = (float) ($this->opening_balance ?? 0);

        $salesImpact = $this->relationLoaded('sales')
            ? (float) $this->sales->sum(function ($sale) {
                return (float) ($sale->grand_total ?? $sale->total_amount ?? 0);
            })
            : (float) $this->sales()
                ->get(['grand_total', 'total_amount'])
                ->sum(function ($sale) {
                    return (float) ($sale->grand_total ?? $sale->total_amount ?? 0);
                });

        if ($this->transaction_type === 'pay') {
            return $openingBalance - $salesImpact;
        }

        if ($this->transaction_type === 'receive') {
            return $openingBalance + $salesImpact;
        }

        return $openingBalance;
    }

    public function getFormattedCurrentBalanceAttribute()
    {
        return number_format((float) $this->current_balance, 2);
    }

    public function paymentIns() {
    return $this->hasMany(PaymentIn::class);
}

}
