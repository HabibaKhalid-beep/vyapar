<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'from_bank_account_id',
        'to_bank_account_id',
        'type',
        'amount',
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'meta' => 'array',
    ];

    public function fromBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'from_bank_account_id');
    }

    public function toBankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'to_bank_account_id');
    }
}
