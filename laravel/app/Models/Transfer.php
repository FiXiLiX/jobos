<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'amount_taken',
        'amount_taken_currency_code',
        'amount_received',
        'amount_received_currency_code',
        'budget_subcategory_id',
    ];

    protected $casts = [
        'amount_taken' => 'decimal:2',
        'amount_received' => 'decimal:2',
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function budgetSubcategory(): BelongsTo
    {
        return $this->belongsTo(BudgetSubcategory::class);
    }
}
