<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceExpense extends Model
{
    protected $fillable = [
        'invoice_id',
        'bardana',
        'mazdori',
        'rehra_mazdori',
        'dak_karaya',
        'brokeri',
        'local_izafi',
        'total_expense',
    ];

    protected $casts = [
        'bardana' => 'decimal:2',
        'mazdori' => 'decimal:2',
        'rehra_mazdori' => 'decimal:2',
        'dak_karaya' => 'decimal:2',
        'brokeri' => 'decimal:2',
        'local_izafi' => 'decimal:2',
        'total_expense' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
