<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionAdjustment extends Model
{
    protected $fillable = [
        'transaction_id',
        'account_party_id',
        'broker_id',
        'item_id',
        'mode',
        'title',
        'details',
        'percentage',
        'rate',
        'amount',
        'affects_invoice',
        'sort_order',
    ];

    protected $casts = [
        'percentage' => 'decimal:4',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'affects_invoice' => 'boolean',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function accountParty()
    {
        return $this->belongsTo(Party::class, 'account_party_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
