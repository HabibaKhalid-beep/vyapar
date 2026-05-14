<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChequeTransaction extends Model
{
    protected $fillable = [
        'type',
        'name',
        'cheque_number',
        'amount',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];
}