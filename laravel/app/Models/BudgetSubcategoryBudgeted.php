<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSubcategoryBudgeted extends Model
{
    protected $fillable = [
        'budget_id',
        'subcategory_id',
        'budgeted',
    ];

    protected $casts = [
        'budgeted' => 'float',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(BudgetSubcategory::class, 'subcategory_id');
    }
}
