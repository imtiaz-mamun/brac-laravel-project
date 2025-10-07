<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'branch_id',
        'loan_amount',
        'interest_rate',
        'issue_date',
        'tenure_months',
        'status'
    ];

    protected $casts = [
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'issue_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    // Accessors
    public function getTotalRepaidAttribute(): float
    {
        return $this->repayments->sum('amount_paid');
    }

    public function getRemainingBalanceAttribute(): float
    {
        $totalAmount = $this->loan_amount * (1 + ($this->interest_rate / 100));
        return $totalAmount - $this->total_repaid;
    }
}
