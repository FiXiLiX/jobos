<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetIncome extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'created_by',
    ];

    public function budgetedAmounts(): HasMany
    {
        return $this->hasMany(BudgetCategoryBudgeted::class, 'budget_income_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
