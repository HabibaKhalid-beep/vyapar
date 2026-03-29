<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChallanDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'challan_number',
        'invoice_date',
        'due_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
