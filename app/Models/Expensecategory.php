<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = ['user_id', 'name', 'type'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->expenses()->sum('total_amount');
    }
}
