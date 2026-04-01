<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
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

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
