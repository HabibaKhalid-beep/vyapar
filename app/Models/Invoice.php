<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'party_id',
        'broker_id',
        'tadad',
        'total_wazan',
        'safi_wazan',
        'rate',
        'amount',
        'deo',
        'broker_commission_type',
        'broker_commission_value',
        'broker_commission',
        'final_amount',
        'notes',
    ];

    protected $casts = [
        'total_wazan' => 'decimal:2',
        'safi_wazan' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'broker_commission_value' => 'decimal:2',
        'broker_commission' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function expenses()
    {
        return $this->hasOne(InvoiceExpense::class);
    }
}
