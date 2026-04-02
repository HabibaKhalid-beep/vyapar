<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id', 'expense_category_id', 'expense_no',
        'expense_date', 'total_amount', 'payment_type',
        'reference_no', 'party', 'balance'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}