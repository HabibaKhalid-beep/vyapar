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
        'broker_name',
        'broker_phone',
        'warehouse_id',
        'warehouse_name',
        'warehouse_phone',
        'warehouse_handler_name',
        'warehouse_handler_phone',
        'responsible_user_id',
        'vehicle_number',
        'destination',
        'delivery_expenses',
        'notification_sent_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'delivery_expenses' => 'decimal:2',
        'notification_sent_at' => 'datetime',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function responsibleUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsible_user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }
}
