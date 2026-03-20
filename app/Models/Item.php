<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
