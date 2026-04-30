<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'phone_number_2',
        'ptcl_number',
        'email',
        'city',
        'address',
        'billing_address',
        'shipping_address',
        'opening_balance',
        'current_balance',
        'as_of_date',
        'credit_limit_enabled',
        'credit_limit_amount',
        'due_days',
        'custom_fields',
        'transaction_type',
        'party_type',
        'party_group',
    ];

    protected $appends = [
        'current_balance',
        'formatted_current_balance',
    ];

    protected $casts = [
        'credit_limit_enabled' => 'boolean',
        'current_balance' => 'decimal:2',
        'credit_limit_amount' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    public function getAsOfDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }

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

    public function getCurrentBalanceAttribute($value)
    {
        if ($value !== null) {
            return (float) $value;
        }

        $signedOpeningBalance = (float) ($this->opening_balance ?? 0);
        if (strtolower((string) $this->transaction_type) === 'pay') {
            $signedOpeningBalance *= -1;
        }

        $salesReceivedAmount = $this->relationLoaded('sales')
            ? (float) $this->sales
                ->whereIn('type', ['invoice', 'pos'])
                ->sum(fn ($sale) => (float) ($sale->received_amount ?? 0))
            : (float) $this->sales()
                ->whereIn('type', ['invoice', 'pos'])
                ->sum('received_amount');

        return $signedOpeningBalance + $salesReceivedAmount;
    }

    public function getFormattedCurrentBalanceAttribute()
    {
        return number_format((float) $this->current_balance, 2);
    }

    public function paymentIns()
    {
        return $this->hasMany(PaymentIn::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
