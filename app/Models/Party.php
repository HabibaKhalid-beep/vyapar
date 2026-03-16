<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'billing_address', 'shipping_address',
        'opening_balance', 'as_of_date', 'credit_limit_enabled', 'custom_fields'
    ];

    protected $casts = [
        'credit_limit_enabled' => 'boolean',
        'custom_fields' => 'array',
    ];

    
}



