<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCategoryBudgeted extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'budget_id',
        'budget_income_id',
        'expected',
        'created_by',
    ];
}
