<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{
    protected $fillable = [
        'party_name',
        'phone',
        'billing_address',
        'shipping_address',
        'order_number',
        'order_date',
        'due_date',
        'total_qty',
        'total_amount',
        'discount_pct',
        'discount_rs',
        'tax_pct',
        'tax_amount',
        'round_off',
        'grand_total',
        'advance_amount',
        'balance',
        'description',
        'image_path',
        'document_path',
    ];

    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_pct' => 'decimal:2',
        'discount_rs' => 'decimal:2',
        'tax_pct' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleOrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SaleOrderPayment::class);
    }
}
