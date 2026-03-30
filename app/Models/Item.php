<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'type', 'name', 'category_id', 'unit',
        'sale_price', 'wholesale_price', 'purchase_price',
        'opening_qty', 'item_code', 'location', 'min_stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
