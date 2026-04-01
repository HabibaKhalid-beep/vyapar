<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'party_id', 'type', 'number', 'date', 'total', 'balance', 'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    
}