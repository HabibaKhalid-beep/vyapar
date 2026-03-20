<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanAccount extends Model
{
    protected $fillable = [
        'display_name',
        'lender_bank_id',
        'account_number',
        'description',
        'current_balance',
        'balance_as_of',
        'received_in',
        'interest_rate',
        'term_months',
        'processing_fee',
        'processing_fee_paid_from_id',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'balance_as_of' => 'date',
    ];

    public function lenderBank()
    {
        return $this->belongsTo(BankAccount::class, 'lender_bank_id');
    }

    public function receivedInBank()
    {
        return $this->belongsTo(BankAccount::class, 'received_in');
    }

    public function processingFeeBank()
    {
        return $this->belongsTo(BankAccount::class, 'processing_fee_paid_from_id');
    }
}
