<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
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
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'as_of_date' => 'date',
        'print_on_invoice' => 'boolean',
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

}
