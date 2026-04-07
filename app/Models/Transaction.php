<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'party_id',
        'counter_party_id',
        'type',
        'number',
        'transfer_group',
        'date',
        'total',
        'balance',
        'status',
        'description',
        'attachment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function counterParty()
    {
        return $this->belongsTo(Party::class, 'counter_party_id');
    }

    
}
