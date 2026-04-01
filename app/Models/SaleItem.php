<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'item_id',
        'item_name',
        'item_category',
        'item_code',
        'item_description',
        'quantity',
        'unit',
        'unit_price',
        'discount',
        'amount',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount'   => 'decimal:2',
        'amount'     => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // ← THIS WAS MISSING
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}