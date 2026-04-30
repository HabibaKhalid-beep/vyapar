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
        'receipt_no',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
