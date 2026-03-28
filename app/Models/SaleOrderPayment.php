<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrderPayment extends Model
{
    protected $fillable = [
        'sale_order_id',
        'payment_type',
        'bank_account_id',
        'amount',
        'reference',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
