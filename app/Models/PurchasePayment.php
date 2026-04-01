<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $fillable = [
        'purchase_id',
        'payment_type',
        'bank_account_id',
        'amount',
        'reference',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
