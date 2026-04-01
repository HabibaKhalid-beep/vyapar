<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'party_id',
        'party_name',
        'phone',
        'billing_address',
        'bill_number',
        'bill_date',
        'total_qty',
        'total_amount',
        'discount_pct',
        'discount_rs',
        'tax_pct',
        'tax_amount',
        'shipping_charge',
        'round_off',
        'grand_total',
        'paid_amount',
        'balance',
        'description',
        'image_path',
        'document_path',
    ];

    protected $casts = [
        'bill_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
