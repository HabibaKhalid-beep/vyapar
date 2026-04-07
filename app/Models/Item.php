<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'type', 'name', 'category_id', 'unit',
        'sale_price', 'wholesale_price', 'purchase_price',
        'opening_qty', 'item_code', 'location', 'description',
        'image_path', 'image_paths', 'min_stock',
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

    protected $appends = ['stock_qty'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function sales()
    {
        return $this->hasManyThrough(
            Sale::class,
            SaleItem::class,
            'item_id',
            'id',
            'id',
            'sale_id'
        );
    }

    public function getStockQtyAttribute(): float
    {
        $sold = $this->saleItems()
            ->whereHas('sale', fn($q) => $q->whereIn('type', [
                'invoice', 'pos'
            ]))
            ->sum('quantity');

        $returned = $this->saleItems()
            ->whereHas('sale', fn($q) => $q->where('type', 'sale_return'))
            ->sum('quantity');

        return max(0, floatval($this->opening_qty) + floatval($returned) - floatval($sold));
    }

    public function getStockValueAttribute(): float
    {
        return $this->stock_qty * floatval($this->purchase_price);
    }
}
