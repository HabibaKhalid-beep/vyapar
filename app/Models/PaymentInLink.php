<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInLink extends Model
{
    protected $fillable = [
        'payment_in_id',
        'sale_id',
        'linked_amount',
    ];

    protected $casts = [
        'linked_amount' => 'decimal:2',
    ];

    public function paymentIn()
    {
        return $this->belongsTo(PaymentIn::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
