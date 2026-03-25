<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class Party extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'opening_balance',
        'as_of_date',
        'credit_limit_enabled',
        'custom_fields',
        'transaction_type',
        'party_type'
    ];

    protected $casts = [
        'credit_limit_enabled' => 'boolean',
        'custom_fields' => 'array',
        // ✅ 'date' cast hatao, neeche accessor use karo
    ];

    // ✅ ADD: as_of_date ko YYYY-MM-DD format mein return karo
    public function getAsOfDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : null;
    }
}


