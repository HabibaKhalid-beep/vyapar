<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $appends = ['display_with_account'];

    protected $fillable = [
        'display_name',
        'opening_balance',
        'as_of_date',
        'account_number',
        'swift_code',
        'iban',
        'bank_name',
        'account_holder_name',
        'print_on_invoice',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'as_of_date' => 'date',
        'print_on_invoice' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function outgoingTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'from_bank_account_id');
    }

    public function incomingTransactions()
    {
        return $this->hasMany(BankTransaction::class, 'to_bank_account_id');
    }

public function paymentIns() {
    return $this->hasMany(PaymentIn::class);
}

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDisplayWithAccountAttribute(): string
    {
        $accountNumber = preg_replace('/\s+/', '', (string) ($this->account_number ?? ''));

        return trim($this->display_name . ($accountNumber !== '' ? ' - ' . $accountNumber : ''));
    }

}
