<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class Barcode extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'item_name',
        'item_code',
        'sale_price',
        'discount',
        'labels',
        'header',
        'line_1',
        'line_2',
        'line_3',
        'line_4',
        'barcode_value',
    ];

    protected $casts = [
        'sale_price' => 'float',
        'discount' => 'float',
        'labels' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
