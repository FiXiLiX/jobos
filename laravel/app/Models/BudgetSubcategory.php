<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetSubcategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
    ];

    protected $casts = [
        //
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BudgetCategory::class, 'category_id');
    }

    public function budgetedAmounts(): HasMany
    {
        return $this->hasMany(BudgetSubcategoryBudgeted::class, 'subcategory_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'budget_subcategory_id');
    }
}
