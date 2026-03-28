<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrderItem extends Model
{
    protected $fillable = [
        'sale_order_id',
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
        'discount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class);
    }
}
