<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    protected $fillable = [
        'party_id',
        'bill_number',
        'estimate_date',
        'total_qty',
        'total_amount',
        'discount_pct',
        'discount_rs',
        'tax_pct',
        'tax_amount',
        'round_off',
        'grand_total',
        'description',
        'image_path',
        'status',
        'converted_sale_id',
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_pct' => 'decimal:2',
        'discount_rs' => 'decimal:2',
        'tax_pct' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'round_off' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'status' => 'string',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function items()
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function convertedSale()
    {
        return $this->belongsTo(Sale::class, 'converted_sale_id');
    }
}
