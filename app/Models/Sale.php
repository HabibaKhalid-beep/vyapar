<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'type',
        'party_id',
        'party_name',
        'phone',
        'billing_address',
        'shipping_address',
        'bill_number',
        'invoice_date',
        'order_date',
        'due_date',
        'reference_id',
        'total_qty',
        'total_amount',
        'discount_pct',
        'discount_rs',
        'tax_pct',
        'tax_amount',
        'round_off',
        'grand_total',
        'received_amount',
        'balance',
        'status',
        'description',
        'image_path',
        'document_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'order_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_pct' => 'decimal:2',
        'discount_rs' => 'decimal:2',
        'tax_pct' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
