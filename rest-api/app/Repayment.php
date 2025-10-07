<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_date',
        'amount_paid',
        'payment_mode',
        'reference_no'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
