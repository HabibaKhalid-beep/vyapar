<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheque extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',           // 'sale' | 'purchase' | 'payment_in' | 'payment_out' | 'other'
        'name',           // party name / description
        'ref_no',         // cheque / reference number
        'transaction_date',
        'cheque_date',
        'amount',
        'status',         // 'open' | 'deposited' | 'bounced' | 'cancelled'
        'bank_account_id',
        'reference_id',   // FK to sales/purchases etc.
        'reference_type',
        'notes',
        'deposited_at',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'cheque_date'      => 'date',
        'deposited_at'     => 'datetime',
        'amount'           => 'decimal:2',
    ];

    /* ── Relationships ── */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Scopes ── */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeDeposited($query)
    {
        return $query->where('status', 'deposited');
    }

    /* ── Helpers ── */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'deposited' => 'Deposited',
            'bounced'   => 'Bounced',
            'cancelled' => 'Cancelled',
            default     => 'Open',
        };
    }
}