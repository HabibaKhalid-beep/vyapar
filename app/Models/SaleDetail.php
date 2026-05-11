<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sale_id',
        'warehouse_id',
        'delivery_person',
        'bilti_no',
        'gate_no',
        'po_no',
        'po_date',
        'city',
        'party_no',
        'goods_name',
        'details_extra',
        'bilti_gari_no',
        'custom_expenses',
    ];

    protected $casts = [
        'po_date' => 'date',
        'custom_expenses' => 'array',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
