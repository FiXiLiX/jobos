<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'year',
        'month',
        'budgeted',
        'spent',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'budgeted' => 'float',
        'spent' => 'float',
    ];
}
